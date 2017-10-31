<?php

use App\Models\Member;  
use Illuminate\Database\Seeder;

class MemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for ($i = 1; $i < 16; $i++):
        MEmber::create([
            'title' => 'Title ' . $i,
            'slug' => 'title-' . $i,
            'body' => $i
        ]);
      endfor;
    }
}
