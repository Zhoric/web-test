<?php
use Illuminate\Database\Seeder;

class ThemeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('theme')->delete();

        DB::table('theme')->insert(array(
            'name' => 'Основы алгоритмизации и программирования',
            'discipline_id' => 1
        ));

        DB::table('theme')->insert(array(
            'name' => 'Основы веб-технологий',
            'discipline_id' => 2
        ));
    }
}