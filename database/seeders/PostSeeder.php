<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $websitesIds = Website::all()->pluck('id')->toArray();

        Post::factory(10)->create([
            'website_id' => $websitesIds[array_rand($websitesIds)],
        ]);
    }
}
