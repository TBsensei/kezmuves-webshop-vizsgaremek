<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMIN FIÓK LÉTREHOZÁSA
        // Így nem kell többet Tinkerrel bajlódnod az adatbázis törlése után!
        User::create([
            'name' => 'Főnök (Admin)',
            'email' => 'admin@webshop.hu',
            'password' => Hash::make('jelszo123'),
            'role' => 'admin',
            'phone' => '+36301234567',
            'address' => '1011 Budapest, Vár utca 1.'
        ]);

        // 2. SIMA VÁSÁRLÓ FIÓK LÉTREHOZÁSA (A teszteléshez)
        User::create([
            'name' => 'Teszt Elek',
            'email' => 'vevo@webshop.hu',
            'password' => Hash::make('jelszo123'),
            'role' => 'user',
            'phone' => '+36209876543',
            'address' => '1134 Budapest, Váci út 45.'
        ]);

        // 3. GYÖNYÖRŰ TESZTTERMÉKEK LÉTREHOZÁSA KATEGÓRIÁKKAL ÉS KÉPEKKEL
        $products = [
            [
                'name' => 'Kék mázas kerámia bögre',
                'description' => 'Kézzel korongozott, egyedi kék mázzal égetett fél literes teásbögre. Mikrohullámú sütőben is használható.',
                'price' => 4500,
                'category' => 'Kerámia',
                // Mivel a Vue kódunkat okosan írtuk meg (getImageUrl), ha 'http'-vel kezdődik a link, azt tökéletesen betölti!
                'image_url' => 'https://placehold.co/600x400/1e3a8a/ffffff?text=Kek+Mazas+Bogre'
            ],
            [
                'name' => 'Gyöngyház nyaklánc',
                'description' => 'Elegáns nyaklánc valódi, csiszolt gyöngyház berakással és ezüst lánccal.',
                'price' => 8900,
                'category' => 'Ékszer',
                'image_url' => 'https://placehold.co/600x400/ec4899/ffffff?text=Gyongyhaz+Nyaklanc'
            ],
            [
                'name' => 'Kézműves levendula szappan',
                'description' => '100% természetes összetevőkből, Tihanyi levendula illóolajjal készült, bőrnyugtató hatású szappan.',
                'price' => 1800,
                'category' => 'Kozmetikum',
                'image_url' => 'https://placehold.co/600x400/8b5cf6/ffffff?text=Levendula+Szappan'
            ],
            [
                'name' => 'Bőr karkötő fém dísszel',
                'description' => 'Valódi marhabőrből fonott uniszex karkötő, rozsdamentes acél csattal.',
                'price' => 6500,
                'category' => 'Ékszer',
                'image_url' => 'https://placehold.co/600x400/78350f/ffffff?text=Bor+Karkoto'
            ],
            [
                'name' => 'Mázas agyagtál',
                'description' => 'Hagyományos mintázattal ellátott, kézzel festett agyagtál, tökéletes gyümölcsök tárolására.',
                'price' => 12500,
                'category' => 'Kerámia',
                'image_url' => 'https://placehold.co/600x400/c2410c/ffffff?text=Mazas+Agyagtal'
            ],
            [
                'name' => 'Illatgyertya üvegben (Vanília)',
                'description' => 'Szójaviaszból öntött, hosszú égésidejű kézműves gyertya, intenzív Bourbon vanília illattal.',
                'price' => 3200,
                'category' => 'Ajándék',
                'image_url' => 'https://placehold.co/600x400/f59e0b/ffffff?text=Vanilia+Gyertya'
            ]
        ];

        // Végigmegyünk a tömbön és mindet lementjük
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
