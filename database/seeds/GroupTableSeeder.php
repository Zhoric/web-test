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

        DB::table('group')->insert(array(
            'studyplan_id' => 2,
            'prefix' => 'ИСб',
            'course' => 4,
            'number' => 3,
            'is_fulltime' => true,
            'name' => 'ИСб-43о'));

        for ($i=4; $i<=73; $i++)
        {
            if ($i > 4 ) {
                $groupId = 0;
                if ($i > 4 && $i < 24){
                    $groupId = 1;
                } else if ($i >= 24 && $i < 54){
                    $groupId = 2;
                } else {
                    $groupId = 3;
                }

                DB::table('student_group')->insert(array(
                    'student_id' => $i,
                    'group_id' => $groupId
                ));
            }
;
        }
    }
}