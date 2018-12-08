<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DisciplineGroupTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('discipline_group')->delete();


        for ($i = 1; $i <= 2; $i++){

            DB::table('discipline_group')->insert(array(
                'group_id' => $i,
                'discipline_id' => $i,
                'studyplan_id' => $i
            ));
        }
    }
}