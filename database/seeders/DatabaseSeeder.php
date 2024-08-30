<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostTags;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user1 = User::create([
            "name" => "kefayet",
            "email" => "kefayeturrahman492@gmail.com",
            "password" => "12345678"
        ]);

        $admin = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        Permission::create(['name' => 'view_articles']);
        Permission::create(['name' => 'list_articles']);
        Permission::create(['name' => 'create_articles']);
        Permission::create(['name' => 'edit_articles']);
        Permission::create(['name' => 'update_articles']);

        Permission::create(['name' => 'view_role']);
        Permission::create(['name' => 'list_role']);
        Permission::create(['name' => 'create_role']);
        Permission::create(['name' => 'edit_role']);
        Permission::create(['name' => 'update_role']);

        $allPermission = Permission::all();
        $admin->givePermissionTo($allPermission);

        $userRole->givePermissionTo("view_articles", "list_articles");

        $user1->assignRole($admin);

        for ($i = 0; $i < 20; $i++) {
            $randomUser = User::create([
                "name" => fake()->name(),
                "email" => fake()->unique()->email(),
                "password" => "12345678"
            ]);
            $randomUser->assignRole($userRole);
        }

        function randomNum()
        {
            return rand(1, 20);
        }



        Tag::create(
            [
                "title" => "js",
                "hashtag" => "#js",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/6/6a/JavaScript-logo.png",
                "user_id" => rand(1, 20)
            ]
        );


        Tag::create(
            [
                "title" => "react",
                "hashtag" => "#react",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/React_Logo_SVG.svg/512px-React_Logo_SVG.svg.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "php",
                "hashtag" => "#php",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "laravel",
                "hashtag" => "#laravel",
                "thumbnail" => "https://en.wikipedia.org/wiki/Laravel#/media/File:Laravel.svg",
                "user_id" => rand(1, 20)
            ]
        );


        Tag::create(
            [
                "title" => "vue",
                "hashtag" => "#vue",
                "thumbnail" => "https://en.wikipedia.org/wiki/Vue.js#/media/File:Vue.js_Logo_2.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "dev",
                "hashtag" => "#dev",
                "thumbnail" => "https://as1.ftcdn.net/v2/jpg/04/08/85/18/1000_F_408851839_FFYJ0y0hYwcbPwTGJMh0Tx4JrLUOuRfz.jpg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "phyton",
                "hashtag" => "#phyton",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/React_Logo_SVG.svg/512px-React_Logo_SVG.svg.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "cakePhp",
                "hashtag" => "#cakePhp",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/en/9/9a/Cake-logo.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "FuelPHP",
                "hashtag" => "#FuelPHP",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/0/03/FuelPHP_logo.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "flask",
                "hashtag" => "#flask",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/3/3c/Flask_logo.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "django",
                "hashtag" => "#django",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/7/75/Django_logo.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "Nodejs",
                "hashtag" => "#nodeJs",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/6/67/NodeJS.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "SQL",
                "hashtag" => "#sql",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/6/6f/Sql_database_shortcut_icon.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "spring boot",
                "hashtag" => "#springBoot",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/7/79/Spring_Boot.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "mongoDB",
                "hashtag" => "#mongoDB",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/MongoDB_Logo.svg/640px-MongoDB_Logo.svg.png",
                "user_id" => rand(1, 20)
            ]
        );


        Tag::create(
            [
                "title" => ".net",
                "hashtag" => "#.net",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/Microsoft_.NET_logo.svg/640px-Microsoft_.NET_logo.svg.png",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "symphony",
                "hashtag" => "#symphony",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/6/60/Symfony2.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "angular",
                "hashtag" => "#angular",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/c/cf/Angular_full_color_logo.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "nextJs",
                "hashtag" => "#nextJs",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/8/8e/Nextjs-logo.svg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "remix",
                "hashtag" => "#remix",
                "thumbnail" => "https://miro.medium.com/v2/resize:fit:720/format:webp/0*jgOAO3wP44o5dCHU.jpg",
                "user_id" => rand(1, 20)
            ]
        );

        Tag::create(
            [
                "title" => "svelte",
                "hashtag" => "#svelte",
                "thumbnail" => "https://upload.wikimedia.org/wikipedia/commons/1/1b/Svelte_Logo.svg",
                "user_id" => rand(1, 20)
            ]
        );


        foreach (range(1, 200) as $index) {
            // Post created date
            $postDate = fake()->dateTimeBetween('-1 month', 'now');

            // Random number between 7
            $randomDay = rand(1, 7);

            // Post published date
            $postPublishObj = clone $postDate;
            $postPublishDate = $postPublishObj->add(new DateInterval("P{$randomDay}D"));

            // Create the post
            $post = Post::create([
                'title' => fake()->sentence(),
                'thumbnail' => fake()->boolean() ? 'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000) : null,
                'body' => fake()->realText(400),
                'active' => fake()->boolean(),
                'published_at' => $postPublishDate,
                'user_id' => fake()->numberBetween(1, 21),
                'created_at' => $postDate,
                'updated_at' => $postDate
            ]);

            foreach (range(1, 5) as $index) {
                PostTags::create([
                    "post_id" => $post->id,
                    "tag_id" => rand(1, 21)
                ]);
            }
        }

        foreach (range(1, 50) as $index) {
            // Post created date
            $postDate = fake()->dateTimeBetween('-1 week', '+1 week');

            // Random number between 7
            $randomDay = rand(1, 14);

            // Post published date
            $postPublishObj = clone $postDate;
            $postPublishDate = $postPublishObj->add(new DateInterval("P{$randomDay}D"));

            // Create the post
            $post = Post::create([
                'title' => fake()->sentence(),
                'thumbnail' => 'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
                'body' => fake()->realText(400),
                'active' => fake()->boolean(),
                'published_at' => $postPublishDate,
                'user_id' => fake()->numberBetween(1, 21),
                'created_at' => $postDate,
                'updated_at' => $postDate
            ]);

            foreach (range(1, 5) as $index) {
                PostTags::create([
                    "post_id" => $post->id,
                    "tag_id" => rand(1, 21)
                ]);
            }
        }

        foreach (range(1, 50) as $index) {

            // Create the post
            $post = Post::create([
                'title' => fake()->sentence(),
                'thumbnail' => 'https://picsum.photos/640/480?random=' . fake()->unique()->numberBetween(1, 1000),
                'body' => fake()->realText(400),
                'active' => fake()->boolean(),
                'published_at' => Carbon::now(),
                'user_id' => fake()->numberBetween(1, 21),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            foreach (range(1, 5) as $index) {
                PostTags::create([
                    "post_id" => $post->id,
                    "tag_id" => rand(1, 21)
                ]);
            }
        }

        // Comment
        foreach (range(1, 300) as $post) {
            foreach (range(1, 10) as $index) {
                $comment = Comment::create([
                    'user_id' => rand(1, 21),
                    'post_id' => $post,
                    'comment' => fake()->realText(400)
                ]);

                $childComment = Comment::create([
                    'user_id' => rand(1, 21),
                    'post_id' => $post,
                    "parent_id" => $comment->id,
                    "comment" => fake()->realText(400)
                ]);
            }
        }

        //profile
        $totalUser = User::count();
        foreach(range(1, $totalUser) as $userId){
            Profile::create([
                'website_url' => fake()->domainName(),
                'location' => fake()->address(),
                'bio' => fake()->bs(),
                'work' => fake()->jobTitle(),
                'education' => fake()->text(22),
                'profile_image' => 'https://randomuser.me/api/portraits/men/' . $userId . '.jpg',
                'user_id' => $userId
            ]);
        }

        //
    }
}
