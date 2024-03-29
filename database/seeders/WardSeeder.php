<?php

namespace Database\Seeders;


use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ward::truncate();
        $csvFile = fopen(base_path("database/data/wards.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstline) {
                Ward::create([
                    "id" => $data['0'],
                    "name" => $data['1'],
                    "type" => $data['2'],
                    "district_id" => $data['3'],
                ]);
            }
            $firstline = false;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        fclose($csvFile);
    }
}
