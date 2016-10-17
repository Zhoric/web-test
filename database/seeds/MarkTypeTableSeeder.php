<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MarkTypeTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('mark_type')->delete();

        for ($i = 1; $i <= 7; $i++){
            DB::table('mark_type')->insert(array(
                'name' => 'Тест по теме '.$i,
                'number' => $i,
                'discipline_plan_id' => 2));

            DB::table('test_mark_type')->insert(array(
                'test_id' => $i,
                'mark_type_id' => $i,
                'semester' => ($i < 5) ? 4 : 5
            ));
        }

        DB::table('mark_type')->insert(array(
            'name' => 'Курсовой проект',
            'number' => 8,
            'discipline_plan_id' => 2));
    }
}