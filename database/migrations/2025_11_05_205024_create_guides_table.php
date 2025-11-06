<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->comment('Справочник гидов, доступных для выбора в охотничьих турах');

            $table->id()->comment('Первичный ключ гида');

            $table->string('name', 255)
                ->comment('Имя гида (как показываем пользователю)');

            $table->unsignedTinyInteger('experience_years')
                ->default(0)
                ->comment('Опыт работы: полных лет (диапазон 0–255, храним как целое)');

            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('Флаг доступности: true — можно выбирать в бронировании, false — скрыт');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
