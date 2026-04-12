<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'name' => 'Administrador AltaEsencia',
            'email' => 'admin@altaesencia.com',
            'phone' => '70000001',
            'user_type' => 'administrativo',
            'document_number' => 'ADM-001',
            'address' => 'Oficina Central AltaEsencia',
            'estado' => 'activo',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        User::query()->create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@altaesencia.com',
            'phone' => '70000002',
            'user_type' => 'cliente',
            'document_number' => 'CLI-001',
            'address' => 'Zona Centro',
            'estado' => 'activo',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }
}
