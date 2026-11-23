<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function __invoke(): Response
    {
        $page = Page::where('slug', 'student')->first();

        return Inertia::render('Student/Index', [
            'page' => $page,
            'scheduleNotice' => 'Расписание занятий на текущий семестр актуально на 2026/2027 учебный год.',
            'callSchedule' => [
                ['pair' => '1 пара', 'time' => '08:30 – 09:50'],
                ['pair' => '2 пара', 'time' => '10:05 – 11:25'],
                ['pair' => '3 пара', 'time' => '11:40 – 13:00'],
                ['pair' => 'Обеденный перерыв', 'time' => '13:00 – 13:40'],
                ['pair' => '4 пара', 'time' => '13:40 – 15:00'],
                ['pair' => '5 пара', 'time' => '15:15 – 16:35'],
            ],
            'clubs' => [
                ['name' => 'IT Incubator & Robotix', 'desc' => 'Разработка собственных стартапов, участие в хакатонах и чемпионатах WorldSkills.'],
                ['name' => 'Turan Debate Club', 'desc' => 'Развитие ораторского мастерства, критического мышления и дебатов на 3 языках.'],
                ['name' => 'Design & Media Hub', 'desc' => 'Студенческий медиацентр: съемка роликов, фоторепортажи и дизайн журналов.'],
                ['name' => 'Спортивные секции', 'desc' => 'Баскетбол, волейбол, настольный теннис, футзал и шахматы.'],
            ],
        ]);
    }
}
