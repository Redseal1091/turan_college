<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Staff;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(): Response
    {
        $page = Page::where('slug', 'about')->first();
        $staff = Staff::orderBy('sort_order')->get();

        return Inertia::render('About/Index', [
            'page' => $page,
            'staff' => $staff,
        ]);
    }
}
