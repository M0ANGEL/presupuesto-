<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario de prueba
        $user = User::factory()->create([
            'name' => 'Usuario Demo',
            'email' => 'demo@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Crear categorías predeterminadas
        $categories = [
            'Alimentación',
            'Transporte',
            'Vivienda',
            'Servicios',
            'Entretenimiento',
            'Salud',
            'Educación',
            'Ropa',
            'Otros',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'user_id' => $user->id,
            ]);
        }

        // Crear wallet inicial
        $user->wallet()->create([
            'stock' => 1000000,
            'saving' => 200000,
            'personal' => 500000,
        ]);
    }
}