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
        // Super Admin
        User::create([
            'name' => 'User',
            'email' => '',
            'email_verified_at' => now(),
            'telegram_id' => null,
            'telegram_chat_id' => null,
            'phone' => '',
            'gender' => 'male',
            'password' => Hash::make('Fuck2020'),
            'role_type' => 'user',
            'active'=> true,
        ]);


       

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('📧 Admin: superadmin@cms.com / admin123');
        $this->command->info('📧 Content: content@cms.com / content123');
        $this->command->info('📧 Test: test@cms.com / test123');
    }
}