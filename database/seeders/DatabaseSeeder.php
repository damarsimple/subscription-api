<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Subscriber;
use App\Models\Website;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(WebsiteSeeder::class);
        $this->call(SubcriberSeeder::class);

        $websiteAll = Website::all();

        foreach (Subscriber::all() as $subscriber) {
            $subscriber->websites()->attach($websiteAll->random(rand(1, 5)));
        }

        $this->call(PostSeeder::class);
    }
}
