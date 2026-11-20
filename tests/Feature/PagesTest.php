<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\CollegeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CollegeSeeder::class);
    }

    public function test_all_college_pages_render_successfully(): void
    {
        $firstSpecialty = Specialty::first();
        $firstNews = News::first();

        $urls = [
            '/',
            '/about',
            '/about/teachers',
            '/specialties',
            '/specialties/'.($firstSpecialty?->slug ?? 'software-development'),
            '/admission',
            '/student',
            '/career',
            '/news',
            '/news/'.($firstNews?->slug ?? 'news-item-1'),
            '/contacts',
            '/admin/login',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_language_switch_route(): void
    {
        $response = $this->get('/locale/kz');
        $response->assertSessionHas('locale', 'kz');

        $responseEn = $this->get('/locale/en');
        $responseEn->assertSessionHas('locale', 'en');

        $responseRu = $this->get('/locale/ru');
        $responseRu->assertSessionHas('locale', 'ru');
    }

    public function test_admission_online_application_submission(): void
    {
        $specialty = Specialty::first();

        $response = $this->post('/admission/apply', [
            'applicant_name' => 'Ерлан Сапаров',
            'phone' => '+7 777 999 8877',
            'email' => 'erlan@example.com',
            'iin' => '080912501234',
            'specialty_id' => $specialty->id,
            'base_education' => '9_grade',
            'notes' => 'Хочу поступить на грант',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('applications', [
            'applicant_name' => 'Ерлан Сапаров',
            'phone' => '+7 777 999 8877',
        ]);
    }

    public function test_admission_online_application_submission_with_multiple_files(): void
    {
        Storage::fake('public');
        $specialty = Specialty::first();

        $documents = [];
        for ($i = 1; $i <= 10; $i++) {
            $documents[] = UploadedFile::fake()->create("document_{$i}.pdf", 100, 'application/pdf');
        }

        $response = $this->post('/admission/apply', [
            'applicant_name' => 'Алихан Сериков',
            'phone' => '+7 777 123 4567',
            'email' => 'alikhan@example.com',
            'specialty_id' => $specialty->id,
            'base_education' => '11_grade',
            'notes' => 'Приложил 10 документов',
            'documents' => $documents,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('applications', [
            'applicant_name' => 'Алихан Сериков',
            'phone' => '+7 777 123 4567',
        ]);
    }

    public function test_filament_admin_dashboard_and_crm_access(): void
    {
        $admin = User::where('email', 'admin@turancollege.kz')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        $responseApplications = $this->actingAs($admin)->get('/admin/applications');
        $responseApplications->assertStatus(200);
    }
}
