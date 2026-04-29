<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kategóriák tábla létrehozása.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // Egyedi név, indexelve a gyors kereséshez
            $table->string('name')->unique()->index();

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Tábla törlése.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
