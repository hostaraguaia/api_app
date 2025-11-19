<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

            // Criar usuário admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@quiz.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Executar seeder de perguntas
        $this->call(QuestionSeeder::class);
    }
}
