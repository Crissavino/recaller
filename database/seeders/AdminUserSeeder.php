<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'cris@recaller.io'],
            [
                'name' => 'Cris',
                'password' => 'Mirkovich187!',
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
            ]
        );
    }
}
