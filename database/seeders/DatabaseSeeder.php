<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'petugas']);

        // Create Admin User
        $user = User::firstOrCreate([
            'email' => 'admin@koperasi.com',
        ], [
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.com',
            'password' => bcrypt('password'),
        ]);

        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }

        $this->call(SavingsTypeSeeder::class);
        $this->call(KoperasiMemberSeeder::class);
        $this->call(SavingsSeeder::class);
    }
}
