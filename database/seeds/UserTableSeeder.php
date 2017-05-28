<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('user')->delete();

        DB::table('user')->insert(array(
            'firstname' => '',
            'lastname' => 'Администратор',
            'email' => 'admin@admin.ru',
            'password' => bcrypt('ssutestsadmin'),
            'active' => true));

        DB::table('user')->insert(array(
            'firstname' => 'для',
            'lastname' => 'Преподаватель',
            'patronymic' => 'отладки',
            'email' => 'lecturer@mail.ru',
            'password' => bcrypt('lecturer'),
            'active' => true));

        foreach (range(1,10) as $index) {
            DB::table('user')->insert([
                'firstname' => "для",
                'lastname' => "Студент",
                'patronymic' => "отладки".$index,
                'email' => "student".$index."@mail.ru",
                'password' => bcrypt("student".$index),
                'active' => true
            ]);
        }
    }
}