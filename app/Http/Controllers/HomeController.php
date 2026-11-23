<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\News;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Specialty;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $page = Page::where('slug', 'home')->first();

        $featuredSpecialties = Specialty::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $latestNews = News::orderByDesc('published_at')
            ->take(6)
            ->get();

        $partners = Partner::orderBy('sort_order')->get();

        $faqs = Faq::orderBy('sort_order')->take(6)->get();

        return Inertia::render('Home/Index', [
            'page' => $page,
            'featuredSpecialties' => $featuredSpecialties,
            'latestNews' => $latestNews,
            'partners' => $partners,
            'faqs' => $faqs,
            'stats' => [
                'employment_rate' => '95%',
                'graduates_count' => '12 500+',
                'grants_yearly' => '240+',
                'partner_companies' => '150+',
            ],
        ]);
    }
}
