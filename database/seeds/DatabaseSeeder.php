<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call('PageTableSeeder');
        $this->call('StateTableSeeder');
        $this->call('CityTableSeeder');
        $this->call('MemberTableSeeder');
        $this->call('DataTableSeeder');
    }
}
