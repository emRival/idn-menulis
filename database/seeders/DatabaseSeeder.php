<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Tag;
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
        // Create admin users
        User::factory()->admin()->create([
            'username' => 'admin1',
            'email' => 'admin1@menulis.id',
            'full_name' => 'Administrator 1',
            'password' => bcrypt('password'),
        ]);

        User::factory()->admin()->create([
            'username' => 'admin2',
            'email' => 'admin2@menulis.id',
            'full_name' => 'Administrator 2',
            'password' => bcrypt('password'),
        ]);

        User::factory()->admin()->create([
            'username' => 'admin3',
            'email' => 'admin3@menulis.id',
            'full_name' => 'Administrator 3',
            'password' => bcrypt('password'),
        ]);

        // Create guru users
        User::factory(10)->guru()->create();

        // Create siswa users
        User::factory(50)->create();

        // Create categories
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
                'description' => 'Panduan langkah demi langkah untuk berbagai hal',
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
            Category::create($category);
        }

        // Create tags
        $tagNames = [
            'Menulis', 'Berkomunikasi', 'Kreatif', 'Pelajaran', 'Inspirasi',
            'Cerita', 'Fiksi', 'Non-Fiksi', 'Produksi', 'Editing',
            'Grammar', 'Gaya Penulisan', 'Tipografi', 'Narasi', 'Argumentasi',
            'Karakter', 'Plot', 'Background', 'Dialog', 'Deskripsi',
        ];

        foreach ($tagNames as $name) {
            Tag::create([
                'name' => $name,
                'slug' => str($name)->slug(),
            ]);
        }

        // Create articles
        $users = User::where('role', 'siswa')->get();
        $tags = Tag::all();
        $categories = Category::all();

        for ($i = 0; $i < 100; $i++) {
            $article = Article::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'title' => fake()->sentence(6),
                'slug' => fake()->slug() . '-' . $i,
                'excerpt' => fake()->paragraph(),
                'content' => fake()->paragraphs(5, true),
                'status' => fake()->randomElement(['draft', 'pending', 'published', 'rejected']),
                'is_featured' => $i < 5 ? true : false,
                'views_count' => fake()->numberBetween(0, 1000),
                'reading_time' => fake()->numberBetween(1, 15),
                'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);

            // Attach random tags
            $randomTags = $tags->random(fake()->numberBetween(1, 5))->pluck('id')->toArray();
            $article->tags()->attach($randomTags);
        }

        // Create comments
        $articles = Article::where('status', 'published')->get();
        $siswaUsers = User::where('role', 'siswa')->get();

        for ($i = 0; $i < 500; $i++) {
            $article = $articles->random();
            $user = $siswaUsers->random();

            Comment::create([
                'article_id' => $article->id,
                'user_id' => $user->id,
                'parent_id' => fake()->boolean(30) ? Comment::where('article_id', $article->id)->whereNull('parent_id')->inRandomOrder()->first()?->id : null,
                'content' => fake()->paragraph(),
                'is_approved' => true,
            ]);
        }

        // Create likes
        $publishedArticles = Article::where('status', 'published')->get();

        for ($i = 0; $i < 1000; $i++) {
            try {
                Like::create([
                    'user_id' => $siswaUsers->random()->id,
                    'article_id' => $publishedArticles->random()->id,
                ]);
            } catch (\Exception $e) {
                // Skip duplicate like constraint violations
                continue;
            }
        }

        // Create bookmarks
        for ($i = 0; $i < 300; $i++) {
            try {
                Bookmark::create([
                    'user_id' => $siswaUsers->random()->id,
                    'article_id' => $publishedArticles->random()->id,
                ]);
            } catch (\Exception $e) {
                // Skip duplicate bookmark constraint violations
                continue;
            }
        }
    }
}

