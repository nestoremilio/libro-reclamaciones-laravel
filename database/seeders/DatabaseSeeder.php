<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Crea el usuario administrador con los datos de ADMIN_* del .env.
     * Si ADMIN_PASSWORD está vacío, genera una contraseña aleatoria y la muestra en consola.
     */
    public function run(): void
    {
        $admin = config('empresa.admin');
        $email = $admin['email'];

        if (User::where('email', $email)->exists()) {
            $this->command?->info("El administrador {$email} ya existe.");

            return;
        }

        $password = $admin['password'] ?: Str::password(16);

        User::create([
            'name'     => $admin['name'],
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $this->command?->info("Administrador creado: {$email}");

        if (! $admin['password']) {
            $this->command?->warn("Contraseña generada (guárdala): {$password}");
        }
    }
}
