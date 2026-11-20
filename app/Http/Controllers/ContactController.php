<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contacts/Index', [
            'contacts' => [
                'address' => 'Республика Казахстан, г. Алматы, ул. Сатпаева 16а',
                'phones' => [
                    ['label' => 'Приемная комиссия', 'number' => '+7 (727) 260-40-35'],
                    ['label' => 'Учебная часть', 'number' => '+7 (727) 260-40-30'],
                    ['label' => 'Горячая линия / WhatsApp', 'number' => '+7 (778) 555-53-65'],
                ],
                'email' => 'turan_college@mail.ru',
                'working_hours' => 'Понедельник — Пятница: 09:00 — 18:00',
            ],
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Спасибо за обращение! Ваше письмо успешно отправлено. Мы ответим вам в ближайшее время.');
    }
}
