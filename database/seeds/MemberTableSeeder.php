<?php

use App\Models\Member;  
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create();
      for ($i = 1; $i < 16; $i++):
        Member::create([
            'title' => $faker->name,
            'slug' => $faker->word,
            'body' => $faker->text(500),
            'photo' => '/img/member.png'
        ]);
      endfor;
    }
}
