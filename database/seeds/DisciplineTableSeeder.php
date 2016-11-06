<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DisciplineTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('discipline')->delete();

        DB::table('discipline')->insert(array(
            'name' => 'Алгоритмизация и программирование',
            'abbreviation' => 'АИП',
            'description' => 'Алгоритмизация и программирование'));
        DB::table('discipline')->insert(array(
            'name' => 'Веб-технологии',
            'abbreviation' => 'WEB',
            'description' => 'Веб-технологии'));
        DB::table('discipline')->insert(array(
            'name' => 'Теория принятия решений',
            'abbreviation' => 'ТПР',
            'description' => 'Теория принятия решений'));
        DB::table('discipline')->insert(array(
            'name' => 'Методы и средства проектирования информационных систем и технологий',
            'abbreviation' => 'МиСПИСиТ',
            'description' => 'Методы и средства проектирования информационных систем и технологий'));
        DB::table('discipline')->insert(array(
            'name' => 'Платформа Java',
            'abbreviation' => 'JAVA',
            'description' => 'Берегите RAM!'));

        for ($i = 1; $i <= 5; $i++){

            DB::table('discipline_lecturer')->insert(array(
                'lecturer_id' => 2,
                'discipline_id' => $i
            ));

            DB::table('profile_discipline')->insert(array(
                'discipline_id' => $i,
                'profile_id' => 1));

            DB::table('discipline_lecturer')->insert(array(
                'discipline_id' => $i,
                'lecturer_id' => 1));
        }


    }
}