<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StudyPlanTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('studyplan')->delete();

        DB::table('studyplan')->insert(array(
            'name' => 'ИС бакалавр очная форма [2017]',
            'profile_id' => 1));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 1,
                'studyplan_id' => 1,
                'start_semester' => 1,
                'semesters_count' => 3,
                'hours' => 350,
                'has_project' => true,
                'has_exam' => true));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 2,
                'studyplan_id' => 1,
                'start_semester' => 4,
                'semesters_count' => 2,
                'hours' => 150,
                'has_project' => false,
                'has_exam' => false));
    }
}