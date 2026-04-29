<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adatbázis táblák létrehozása.
     */
    public function up(): void
    {
        // Felhasználók táblája kiegészítve webshop funkciókkal
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Jogosultság és kapcsolattartási adatok
            $table->string('role')->default('user')->index(); // Indexelve a gyorsabb lekéréshez
            $table->string('phone')->nullable();
            $table->string('address', 500)->nullable(); // Kicsit hosszabb címeknek is legyen hely

            $table->rememberToken();
            $table->timestamps();
        });

        // Jelszó visszaállításhoz szükséges tokenek
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Munkamenetek (Session) tárolása - Laravel 11 kompatibilis
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Táblák eltávolítása (Rollback esetén).
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
