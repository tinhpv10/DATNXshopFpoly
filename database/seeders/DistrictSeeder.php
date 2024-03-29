<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        District::truncate();
        $csvFile = fopen(base_path("database/data/districts.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstline) {
                District::create([
                    "id" => $data['0'],
                    "name" => $data['1'],
                    "type" => $data['2'],
                    "province_id" => $data['3'],
                ]);
            }
            $firstline = false;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        fclose($csvFile);
    }
}
