<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_participant_id')->constrained('event_participants')->cascadeOnDelete();
            $table->unique(['competition_id', 'event_participant_id'], 'comp_part_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};
