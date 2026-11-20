<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Faq;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdmissionController extends Controller
{
    public function index(): Response
    {
        $specialties = Specialty::where('is_active', true)
            ->select('id', 'title', 'code', 'base_education')
            ->get();

        $faqs = Faq::where('category', 'Поступление')
            ->orWhere('category', 'Общежитие')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admission/Index', [
            'specialties' => $specialties,
            'faqs' => $faqs,
        ]);
    }

    public function apply(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'iin' => 'nullable|string|max:12',
            'specialty_id' => 'required|exists:specialties,id',
            'base_education' => 'required|in:9_grade,11_grade',
            'notes' => 'nullable|string|max:2000',
            'documents' => 'nullable|array|max:10',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'photo' => 'nullable|image|max:10240',
        ]);

        $docPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                if ($file->isValid()) {
                    $docPaths[] = $file->store('applications/documents', 'public');
                }
            }
        }

        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('applications/photos', 'public');
        }

        $validated['documents'] = ! empty($docPaths) ? $docPaths : null;
        $validated['photo'] = $photoPath;

        Application::create($validated);

        return back()->with('success', 'Ваше заявление успешно отправлено! Специалист приемной комиссии свяжется с вами в течение дня.');
    }
}
