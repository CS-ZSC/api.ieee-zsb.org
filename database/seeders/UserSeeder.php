<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Find the IEEE chapter
        $ieeeChapter = Chapter::where('short_name', 'IEEE')->firstOrFail();

        // Create a user in the IEEE chapter (no track)
        $ieeeChapter->users()->firstOrCreate(
            ['email' => 'abdallahhamada2103@gmail.com'],
            [
                'name' => 'Abdullah Awadallah',
                'password' => Hash::make('12345678'),
                'position' => 'dev',
                "avatar_src" => "https://media.licdn.com/dms/image/v2/D4D03AQHj8Asfo4ZVrQ/profile-displayphoto-crop_800_800/B4DZuxtrYRIcAI-/0/1768213102726?e=1770249600&v=beta&t=w0Ral82eoM3_IbUEfrWnOxa7tY5KMDem6KJCzWv9j4g",
                "linkedin" => "https://www.linkedin.com/in/abdallah-awadallah-4331a7298/",
                'email_verified_at' => now(),
            ]
        );
    }
}
