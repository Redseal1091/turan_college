<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SpecialtyController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Specialty::where('is_active', true);

        if ($request->filled('department')) {
            $query->where('department', $request->input('department'));
        }

        if ($request->filled('base')) {
            $base = $request->input('base');
            $query->where(function ($q) use ($base) {
                $q->where('base_education', $base)
                    ->orWhere('base_education', 'both');
            });
        }

        $specialties = $query->orderBy('sort_order')->orderBy('title')->get();

        $departments = Specialty::where('is_active', true)
            ->distinct()
            ->pluck('department');

        return Inertia::render('Specialties/Index', [
            'specialties' => $specialties,
            'departments' => $departments,
            'filters' => $request->only(['department', 'base']),
        ]);
    }

    public function show(string $slug): Response
    {
        $specialty = Specialty::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $related = Specialty::where('department', $specialty->department)
            ->where('id', '!=', $specialty->id)
            ->take(3)
            ->get();

        return Inertia::render('Specialties/Show', [
            'specialty' => $specialty,
            'related' => $related,
        ]);
    }
}
