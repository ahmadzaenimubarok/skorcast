<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed akun admin.
 *
 * Cara pakai:
 *   ADMIN_PASSWORD='passwordkuat' php artisan db:seed --class=AdminUserSeeder
 *
 * Jika ADMIN_PASSWORD tidak di-set, password acak dibuat & ditampilkan sekali.
 * Idempotent — aman dijalankan ulang (updateOrCreate by email).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@gmail.com');
        $password = env('ADMIN_PASSWORD', Str::random(16));

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => $password,
            ]
        );

        if (! env('ADMIN_PASSWORD')) {
            $this->command->info("Admin {$email} dibuat. Password: {$password}");
        }

        // Akun wasit (role non-admin, boleh akses panel wasit)
        $wasitEmail = env('WASIT_EMAIL', 'wasit@gmail.com');
        $wasitPassword = env('WASIT_PASSWORD', Str::random(16));

        User::updateOrCreate(
            ['email' => $wasitEmail],
            [
                'name' => 'Wasit',
                'role' => 'wasit',
                'password' => $wasitPassword,
            ]
        );

        if (! env('WASIT_PASSWORD')) {
            $this->command->info("Wasit {$wasitEmail} dibuat. Password: {$wasitPassword}");
        }
    }
}
