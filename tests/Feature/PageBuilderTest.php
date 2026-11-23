<?php

namespace Tests\Feature;

use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\CollegeSeeder;
use Database\Seeders\PageSeeder;
use Filament\Support\Enums\Width;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            CollegeSeeder::class,
            PageSeeder::class,
        ]);
    }

    public function test_all_six_core_pages_exist_with_content(): void
    {
        $expectedPages = ['home', 'about', 'admission', 'student', 'career', 'contacts'];

        foreach ($expectedPages as $slug) {
            $page = Page::where('slug', $slug)->first();
            $this->assertNotNull($page, "Page with slug {$slug} should exist.");
            $this->assertTrue($page->is_active);
            $this->assertIsArray($page->content);
            $this->assertNotEmpty($page->content);
        }
    }

    public function test_page_content_structure_for_all_pages(): void
    {
        $home = Page::where('slug', 'home')->firstOrFail();
        $this->assertEquals('TURAN COLLEGE', $home->content['hero']['title']);
        $this->assertArrayHasKey('about_preview', $home->content);
        $this->assertArrayHasKey('cta_banner', $home->content);

        $about = Page::where('slug', 'about')->firstOrFail();
        $this->assertArrayHasKey('pillars', $about->content);
        $this->assertCount(6, $about->content['pillars']);

        $admission = Page::where('slug', 'admission')->firstOrFail();
        $this->assertArrayHasKey('deadlines', $admission->content);
        $this->assertArrayHasKey('steps', $admission->content);
        $this->assertArrayHasKey('required_documents', $admission->content);

        $student = Page::where('slug', 'student')->firstOrFail();
        $this->assertArrayHasKey('call_schedule', $student->content);
        $this->assertArrayHasKey('student_clubs', $student->content);
        $this->assertArrayHasKey('infrastructure', $student->content);

        $career = Page::where('slug', 'career')->firstOrFail();
        $this->assertArrayHasKey('metrics', $career->content);
        $this->assertArrayHasKey('resume_guide', $career->content);

        $contacts = Page::where('slug', 'contacts')->firstOrFail();
        $this->assertArrayHasKey('phones', $contacts->content);
        $this->assertArrayHasKey('2gis_url', $contacts->content);
    }

    public function test_admin_can_access_filament_page_builder(): void
    {
        $admin = User::first();
        $this->actingAs($admin);

        $response = $this->get('/admin/pages');
        $response->assertStatus(200);

        $homePage = Page::where('slug', 'home')->firstOrFail();
        $editResponse = $this->get("/admin/pages/{$homePage->id}/edit");
        $editResponse->assertStatus(200);
    }

    public function test_all_public_pages_render_successfully(): void
    {
        $routes = [
            '/' => 'Home/Index',
            '/about' => 'About/Index',
            '/admission' => 'Admission/Index',
            '/student' => 'Student/Index',
            '/career' => 'Career/Index',
            '/contacts' => 'Contacts/Index',
        ];

        foreach ($routes as $url => $component) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertInertia(fn ($page) => $page
                ->component($component)
                ->has('page')
            );
        }
    }

    public function test_admin_page_resource_uses_full_width(): void
    {
        $admin = User::first();
        $page = Page::first();

        $editPage = new EditPage;
        $this->assertEquals(Width::Full, $editPage->getMaxContentWidth());

        $listPages = new ListPages;
        $this->assertEquals(Width::Full, $listPages->getMaxContentWidth());

        $response = $this->actingAs($admin)->get("/admin/pages/{$page->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Редактор страницы:');
    }
}
