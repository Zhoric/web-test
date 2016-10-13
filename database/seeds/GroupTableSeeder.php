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
            'name' => 'ИCб-41о'));

        DB::table('group')->insert(array(
            'studyplan_id' => 1,
            'prefix' => 'ИСб',
            'course' => 4,
            'number' => 2,
            'is_fulltime' => true,
            'name' => 'ИCб-42о'));

        DB::table('group')->insert(array(
            'studyplan_id' => 2,
            'prefix' => 'ИСб',
            'course' => 4,
            'number' => 3,
            'is_fulltime' => true,
            'name' => 'ИCб-43о'));

        for ($i=1; $i<=70; $i++)
        {
            DB::table('student_group')->insert(array(
                'student_id' => $i,
                'group_id' => ($i > 2 && i < 24) ? 1 : ($i < 54) ? 2 : 3
            ));
        }
    }
}