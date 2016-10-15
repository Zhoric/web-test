<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('InstituteTableSeeder');
        $this->call('ProfileTableSeeder');
        $this->call('UserTableSeeder');
        $this->call('RoleTableSeeder');
        $this->call('DisciplineTableSeeder');
        $this->call('StudyPlanTableSeeder');
        $this->call('GroupTableSeeder');
        $this->call('ThemeTableSeeder');
        $this->call('QuestionTableSeeder');
    }
}
