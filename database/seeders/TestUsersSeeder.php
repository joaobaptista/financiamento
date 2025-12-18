<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Local',
                'email' => 'admin.local@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Criador Teste',
                'email' => 'creator.local@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Apoiador Teste',
                'email' => 'backer.local@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                ['name' => $user['name'], 'password' => $user['password']]
            );
        }

        $this->command?->info('Test users created/updated successfully.');
    }
}
