<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'name' => 'Support Agent',
            'email' => 'agent@example.com',
            'password' => 'password',
            'role' => 'agent',
        ]);
    }
}