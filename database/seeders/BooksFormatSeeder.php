<?php
namespace Database\Seeders;

use App\Models\Format;
use App\Services\Helpers;
use Illuminate\Database\Seeder;

class BooksFormatSeeder extends Seeder
{
    public function run()
    {
        $formats = Helpers::csvToArray(__DIR__ . '/assets/formats.csv');

        foreach($formats as $key => $format)
        {
            if($key == 0) continue;

            if(!Format::where('format_id', $format[0])->exists()) {
                Format::create([
                    'format_id' => $format[0],
                    'format_name' => $format[1] ? : null
                ]);
            }
        }

        unset($formats);
    }
}
