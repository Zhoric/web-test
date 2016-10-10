<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProfileTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('profile')->delete();

        DB::table('profile')->insert(array(
            'institute_id' => '1',
            'code' => '09.03.02',
            'name' => 'ИСиТ',
            'fullname' =>'Информационные системы и технологии',
            'semesters' => 8));

        DB::table('profile')->insert(array(
            'institute_id' => '1',
            'code' => '09.03.01',
            'name' => 'ИВТ',
            'fullname' =>'Информатика и вычислительная техника',
            'semesters' => 8));
    }
}