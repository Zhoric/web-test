<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoleTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('roles')->delete();

        DB::table('roles')->insert(array(
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Администратор'));

        DB::table('roles')->insert(array(
            'name' => 'Lecturer',
            'slug' => 'lecturer',
            'description' => 'Преподаватель'));

        DB::table('roles')->insert(array(
            'name' => 'Student',
            'slug' => 'student',
            'description' => 'Студент'));

        DB::table('roles')->insert(array(
            'name' => 'Employee',
            'slug' => 'employee',
            'description' => 'Сотрудник деканата'));


        DB::table('role_user')->delete();

        DB::table('role_user')->insert([
            'role_id' => UserRole::Admin,
            'user_id' => 1,
        ]);

        DB::table('role_user')->insert([
            'role_id' => UserRole::Lecturer,
            'user_id' => 2,
        ]);

        DB::table('role_user')->insert([
            'role_id' => UserRole::Lecturer,
            'user_id' => 3,
        ]);

        for ($i = 3; $i <= 73; $i++){
            DB::table('role_user')->insert([
                'role_id' => 3,
                'user_id' => $i,
            ]);
        }
    }
}