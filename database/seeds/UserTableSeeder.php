<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('user')->delete();

        DB::table('user')->insert(array(
            'firstname' => 'Админ',
            'lastname' => 'Админов',
            'patronymic' => 'Админович',
            'email' => 'ivan@gmail.com',
            'password' => bcrypt('123456')));

        DB::table('user')->insert(array(
            'firstname' => 'Никита',
            'lastname' => 'Жихарев',
            'patronymic' => 'Евгеньевич',
            'email' => 'test@gmail.com',
            'password' => bcrypt('123456')));

        DB::table('user')->insert(array(
            'firstname' => 'Преподов',
            'lastname' => 'Препод',
            'patronymic' => 'Преподович',
            'email' => 'test2@gmail.com',
            'password' => bcrypt('123456')));

        $faker = Faker::create('ru_RU');
        foreach (range(1,70) as $index) {
            DB::table('user')->insert([
                'firstname' => $faker->firstNameMale,
                'lastname' => $faker->lastName,
                'patronymic' => $faker->middleNameMale,
                'email' => $faker->email,
                'password' => bcrypt('123456'),
            ]);
        }
    }
}