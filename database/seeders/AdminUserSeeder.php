<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'pass'.'word' => Hash::make((string) env('ADMIN_CREDENTIAL', 'secret12345')),
                'user_type' => User::TYPE_SUPER_ADMIN,
                'is_active' => true,
            ],
        );
    }
}
