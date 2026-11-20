<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // e.g. 06120100
            $table->string('title'); // Разработка программного обеспечения
            $table->string('slug')->unique();
            $table->string('qualification')->nullable(); // Техник-программист
            $table->string('department'); // IT, Дизайн, Экономика, Туризм, Право
            $table->string('base_education')->default('9_grade'); // 9_grade, 11_grade, both
            $table->string('duration')->default('2 года 10 месяцев');
            $table->string('study_form')->default('Дневная (очная)');
            $table->text('description')->nullable();
            $table->json('skills')->nullable();
            $table->json('career_prospects')->nullable();
            $table->unsignedInteger('tuition_fee')->default(480000);
            $table->unsignedInteger('grants_count')->default(25);
            $table->string('icon_name')->default('Code');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
