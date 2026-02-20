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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('slug')->unique();
            $table->text('overview')->nullable();
            $table->text('description')->nullable();
            $table->text('logo')->nullable();
            $table->text('cover_image')->nullable();
            $table->timestampTz('start_date');
            $table->timestampTz('end_date');
            $table->text('location');
            $table->text('status');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
