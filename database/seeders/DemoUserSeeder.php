<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Seed the single configured account used by the disposable demo.
     */
    public function run(): void
    {
        $demoUser = config('kers.demo_user');

        User::query()->updateOrCreate(
            ['email' => $demoUser['email']],
            [
                'name' => $demoUser['name'],
                'password' => Hash::make($demoUser['password']),
            ],
        );
    }
}
