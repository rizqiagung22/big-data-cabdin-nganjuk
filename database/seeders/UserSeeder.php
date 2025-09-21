<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // super admin
        User::create([
            'name' => 'Rizqi Agung Dwi Nugraha, S.T.',
            'username' => 'rizqi123',
            'gender' => 'L',
            'password' => Hash::make('123'),
            'role' => 'superadmin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Moh. Nahrowi, S.Kom',
            'username' => 'nahrowi123',
            'gender' => 'L',
            'password' => Hash::make('nahrowi123'),
            'role' => 'superadmin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        // user
        User::create([
            'name' => 'Iwan Triyono, S.H.',
            'username' => 'iwan',
            'gender' => 'L',
            'password' => Hash::make('iwan'),
            'role' => 'user',
            'position' => 'Pejabat/Esselon',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Tekey Widiastuti, SE',
            'username' => 'tekey',
            'gender' => 'P',
            'password' => Hash::make('tekey'),
            'role' => 'user',
            'position' => 'Pejabat/Esselon',
            'email_verified_at' => now(),
        ]);
        // admin
        User::create([
            'name' => 'Risma Cintya Wati, SH.',
            'username' => 'risma',
            'gender' => 'P',
            'password' => Hash::make('risma'),
            'role' => 'admin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Aning Wahyu Lianti, S.Pd.',
            'username' => 'aning',
            'gender' => 'P',
            'password' => Hash::make('aning'),
            'role' => 'admin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Indonesiana Novita, S.Pd.',
            'username' => 'novi',
            'gender' => 'P',
            'password' => Hash::make('novi'),
            'role' => 'admin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Priyo Budiarto, S.Pd., SM., M.AP.',
            'username' => 'priyo',
            'gender' => 'L',
            'password' => Hash::make('priyo'),
            'role' => 'admin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Anggrahita Satrio Wiwoho, SE.',
            'username' => 'angga',
            'gender' => 'L',
            'password' => Hash::make('angga'),
            'role' => 'admin',
            'position' => 'Staf',
            'email_verified_at' => now(),
        ]);
    }
}
