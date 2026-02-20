<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('national_id')->nullable();
            $table->string('password')->nullable(); // hashed password
            $table->string('verification_code', 6);
            $table->string('type')->default('registration'); // 'registration' or 'password_reset'
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique('email');
            $table->index(['email', 'verification_code', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verifications');
    }
};
