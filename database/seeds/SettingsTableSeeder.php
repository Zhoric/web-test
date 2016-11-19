<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use TestEngine\GlobalTestSettings;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('test_settings')->delete();

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::firstSemesterMounthKey,
            'value' => GlobalTestSettings::firstSemesterMounth
        ));

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::secondSemesterMounthKey,
            'value' => GlobalTestSettings::secondSemesterMounth
        ));

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::defaultComplexityKey,
            'value' => GlobalTestSettings::defaultComplexity
        ));

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::maxMarkValueKey,
            'value' => GlobalTestSettings::maxMarkValue
        ));

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::testEndToleranceKey,
            'value' => GlobalTestSettings::testEndTolerance
        ));

        DB::table('test_settings')->insert(array(
            'key' => GlobalTestSettings::questionEndToleranceKey,
            'value' => GlobalTestSettings::questionEndTolerance
        ));

    }
}