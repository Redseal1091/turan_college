<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $query = News::query();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $news = $query->orderByDesc('published_at')->paginate(12)->withQueryString();

        $categories = News::distinct()->pluck('category');

        return Inertia::render('News/Index', [
            'news' => $news,
            'categories' => $categories,
            'currentCategory' => $request->input('category'),
        ]);
    }

    public function show(string $slug): Response
    {
        $article = News::where('slug', $slug)->firstOrFail();

        $recentNews = News::where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $specialties = Specialty::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'title']);

        return Inertia::render('News/Show', [
            'article' => $article,
            'recentNews' => $recentNews,
            'specialties' => $specialties,
        ]);
    }
}
