<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class StudyPlanTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('studyplan')->delete();

        DB::table('studyplan')->insert(array(
            'name' => 'ИС бакалавр очная форма [2016]',
            'profile_id' => 1));

        DB::table('studyplan')->insert(array(
            'name' => 'ГеоИС бакалавр очная форма [2016]',
            'profile_id' => 1));

        for ($i = 1; $i<3; $i++){
            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 1,
                'studyplan_id' => $i,
                'start_semester' => 1,
                'semesters_count' => 3,
                'hours' => 350,
                'has_project' => true,
                'has_exam' => true));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 2,
                'studyplan_id' => $i,
                'start_semester' => 1,
                'semesters_count' => 2,
                'hours' => 150,
                'has_project' => false,
                'has_exam' => false));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 3,
                'studyplan_id' => $i,
                'start_semester' => 4,
                'semesters_count' => 2,
                'hours' => 250,
                'has_project' => false,
                'has_exam' => true));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 4,
                'studyplan_id' => $i,
                'start_semester' => 6,
                'semesters_count' => 2,
                'hours' => 175,
                'has_project' => true,
                'has_exam' => true));

            DB::table('discipline_plan')->insert(array(
                'discipline_id' => 5,
                'studyplan_id' => $i,
                'start_semester' => 5,
                'semesters_count' => 1,
                'hours' => 120,
                'has_project' => false,
                'has_exam' => false));
        }


    }
}