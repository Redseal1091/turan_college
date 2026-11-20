<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Языковой переключатель (KZ, RU, EN)
Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['kz', 'ru', 'en'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

// 1. Главная
Route::get('/', HomeController::class)->name('home');

// 2. Про колледж
Route::get('/about', AboutController::class)->name('about');
Route::get('/about/teachers', TeacherController::class)->name('about.teachers');

// 3. Специальности
Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties.index');
Route::get('/specialties/{slug}', [SpecialtyController::class, 'show'])->name('specialties.show');

// 4. Абитуриенту
Route::get('/admission', [AdmissionController::class, 'index'])->name('admission');
Route::post('/admission/apply', [AdmissionController::class, 'apply'])->name('admission.apply');

// 5. Студенту
Route::get('/student', StudentController::class)->name('student');

// 6. Карьера
Route::get('/career', CareerController::class)->name('career');

// 7. Новости
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// 8. Контакты
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts');
Route::post('/contacts/send', [ContactController::class, 'send'])->name('contacts.send');

// 9. Шаблон страницы
Route::get('/template', function () {
    return Inertia::render('Template/Index');
})->name('template');

// 10. Ошибка 404
Route::get('/404', function () {
    return Inertia::render('Error/404');
})->name('error.404');

Route::fallback(function () {
    return Inertia::render('Error/404')->toResponse(request())->setStatusCode(404);
});
