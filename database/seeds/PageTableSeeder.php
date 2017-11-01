<?php

# database/seeds/PageTableSeeder.php

use App\Models\Page;  
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PageTableSeeder extends Seeder  
{
    public function run()
    {
      $faker = Faker::create();
      $pages = ['About', 'Privacy Policy', 'Terms of Use', 'Credits'];
      for ($i = 0; $i < count($pages); $i++):
        $page = $pages[$i];
        Page::create([
            'title' => $page,
            'slug' => str_slug($page, '-'),
            'body' => $faker->text(1000)
        ]);
      endfor;
    }
}