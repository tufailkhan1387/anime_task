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
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->integer('mal_id')->unique();
            $table->json('titles');
            $table->json('slug');
            $table->text('synopsis')->nullable(); // example of additional data
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
