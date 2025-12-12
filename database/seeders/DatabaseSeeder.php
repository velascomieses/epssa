<?php

namespace Database\Seeders;

use App\Models\Contrato;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'admin@funerariamello.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('123'),
            ]
        );

        $this->call([
            DestinoPagoSeeder::class,
            MedioPagoSeeder::class,
            RoleSeeder::class,
            AtributoSeeder::class,
        ]);

        $user = User::find(1);

        if ($user) {
            $user->assignRole('super-admin');
        }
    }
}
