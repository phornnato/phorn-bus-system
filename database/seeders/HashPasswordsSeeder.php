<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ Hash existing passwords
        $users = DB::table('users_role')->get();

        foreach ($users as $user) {
            DB::table('users_role')
                ->where('id', $user->id)
                ->update([
                    'password' => Hash::make($user->password)
                ]);
        }

        // 2️⃣ Optionally, create a test admin user
        $adminExists = DB::table('users_role')->where('username', 'admin')->exists();

        if (! $adminExists) {
            DB::table('users_role')->insert([
                'username'   => 'admin',
                'email'      => 'admin@example.com',
                'password'   => Hash::make('admin123'), // bcrypt hashed password
                'image'      => 'admin.jpg',
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Passwords hashed and test admin user created.');
    }
}
