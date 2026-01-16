<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->nullableMorphs('scopeable'); // Creates scopeable_type and scopeable_id
            $table->timestamps();

            // Ensure a user can't have the same role twice in the same scope
            $table->unique(['user_id', 'role_id', 'scopeable_type', 'scopeable_id'], 'user_role_scope_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
};
