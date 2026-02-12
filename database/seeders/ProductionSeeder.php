<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Main Admin
        if (!User::where('email', 'admin@idnbogor.id')->exists()) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@idnbogor.id',
                'full_name' => 'Super Administrator',
                'password' => Hash::make('password_aman_disini'), // Ganti nanti!
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'security_key' => null, // Optional
            ]);
            $this->command->info('Admin user created: admin@idnbogor.id / password_aman_disini');
        }

        // 2. Create Categories (Static Data)
        $categories = [
            [
                'name' => 'Tips & Trik',
                'slug' => 'tips-trik',
                'description' => 'Tips dan trik untuk meningkatkan kemampuan menulis',
                'icon' => '💡',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Tutorial',
                'slug' => 'tutorial',
                'description' => 'Panduan langkah demi langkah',
                'icon' => '📚',
                'color' => '#22C55E',
            ],
            [
                'name' => 'Opini',
                'slug' => 'opini',
                'description' => 'Berbagi pendapat dan perspektif',
                'icon' => '💬',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Edukasi',
                'slug' => 'edukasi',
                'description' => 'Konten edukatif untuk pembelajaran',
                'icon' => '🎓',
                'color' => '#EF4444',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // 3. Create Tags (Static Data)
        $tags = ['Menulis', 'Berkomunikasi', 'Kreatif', 'Pelajaran', 'Inspirasi', 'Cerita', 'Fiksi', 'Non-Fiksi', 'Produksi'];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name]
            );
        }
    }
}
