<?php

use App\Models\Guide;
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
        Schema::create('hunting_bookings', function (Blueprint $table) {
            $table->comment('Заявки на охотничьи туры');

            $table->id()->comment('Первичный ключ бронирования');

            $table->string('tour_name', 255)
                ->comment('Название тура');

            $table->string('hunter_name', 255)
                ->comment('Имя клиента');

            $table->foreignIdFor(Guide::class)
                ->comment('Ссылка на выбранного гида')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('date')
                ->comment('Дата проведения тура');

            $table->unsignedTinyInteger('participants_count')
                ->default(1)
                ->comment('Количество участников');

            $table->unique(['guide_id', 'date'], 'uniq_hunting_bookings_guide_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hunting_bookings');
    }
};
