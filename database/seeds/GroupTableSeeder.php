<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class GroupTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('group')->delete();

        DB::table('group')->insert(array(
            'studyplan_id' => 1,
            'prefix' => 'ИСб',
            'course' => 4,
            'number' => 1,
            'is_fulltime' => true,
            'name' => 'ИСб-41о'));

        DB::table('group')->insert(array(
            'studyplan_id' => 1,
            'prefix' => 'ИСб',
            'course' => 4,
            'number' => 2,
            'is_fulltime' => true,
            'name' => 'ИСб-42о'));

        for ($i=3; $i<=12; $i++)
        {
            DB::table('student_group')->insert(array(
                'student_id' => $i,
                'group_id' => 1
            ));
        }
    }
}