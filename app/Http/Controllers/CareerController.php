<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Partner;
use Inertia\Inertia;
use Inertia\Response;

class CareerController extends Controller
{
    public function __invoke(): Response
    {
        $page = Page::where('slug', 'career')->first();
        $partners = Partner::orderBy('sort_order')->get();

        $vacancies = [
            [
                'title' => 'Junior Web-разработчик (Laravel / Vue.js)',
                'company' => 'Smart Solutions KZ',
                'type' => 'Стажировка с трудоустройством',
                'salary' => 'от 220 000 ₸',
                'requirements' => 'Знание основ PHP, JS, Git, аккуратность в верстке.',
            ],
            [
                'title' => 'Графический дизайнер / Контент-мейкер',
                'company' => 'Kcell Marketing Team',
                'type' => 'Частичная занятость (для студентов)',
                'salary' => 'по результатам собеседования',
                'requirements' => 'Владение Figma, Illustrator, портфолио работ.',
            ],
            [
                'title' => 'Помощник бухгалтера',
                'company' => 'Halyk Audit Partners',
                'type' => 'Полная занятость',
                'salary' => 'от 200 000 ₸',
                'requirements' => 'Базовые навыки в 1С:Предприятие 8.3, внимательность.',
            ],
        ];

        return Inertia::render('Career/Index', [
            'page' => $page,
            'partners' => $partners,
            'vacancies' => $vacancies,
        ]);
    }
}
