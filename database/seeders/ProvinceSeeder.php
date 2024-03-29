<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Province::truncate();
        $csvFile = fopen(base_path("database/data/provinces.csv"), "r");

        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstline) {
                Province::create([
                    "id" => $data['0'],
                    "name" => $data['1'],
                    "type" => $data['2'],
                ]);
            }
            $firstline = false;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        fclose($csvFile);

    }
}
