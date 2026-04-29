<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Rendelések és rendelési tételek létrehozása.
     */
    public function up(): void
    {
        // Fő rendelési adatok
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Ha a user törlődik, a rendelés megmarad névtelenül (számvitel miatt fontos!)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->unsignedInteger('total_amount'); // Ár nem lehet negatív
            $table->string('status')->default('pending')->index(); // Indexelve a Dashboard szűrésekhez
            $table->text('shipping_address'); // A szállítási cím legyen kötelező és bővíthető

            $table->timestamps();
        });

        // Konkrét tételek a rendelésben
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Ha a fő rendelést töröljük, a tételek is törlődjenek (Cascade)
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            // Ha egy terméket törlünk a kínálatból, ne vesszen el a rendelési tétel (Restrict)
            // Megjegyzés: Így a rendszer nem engedi törölni a terméket, amíg van hozzá rendelés.
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('restrict');

            $table->unsignedInteger('quantity');
            $table->unsignedInteger('price'); // Az eladási ár rögzítése a rendelés pillanatában

            $table->timestamps();
        });
    }

    /**
     * Visszavonás.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
