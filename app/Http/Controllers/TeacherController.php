<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function __invoke(): Response
    {
        $teachers = Staff::orderBy('sort_order')->get();

        return Inertia::render('About/Teachers', [
            'teachers' => $teachers,
        ]);
    }
}
