<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Post;


class ProvinceImport implements ToCollection
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Access individual data from the row
            $date = $row[0];
            $temperature = $row[1];
            $humidity = $row[2];
            $rainfall = $row[3];
            $windspeed = $row[4];

            // Access additional data from your form or any other source
            $provinsi = request('provinsi'); // Change this to the actual source of your provinsi data
            $kabupaten = request('kabupaten'); // Change this to the actual source of your kabupaten data

            // Perform your logic to store or process the data
            // For example, you can create a new Post model and store the data
            Post::create([
                'date' => $date,
                'temperature' => $temperature,
                'humidity' => $humidity,
                'rainfall' => $rainfall,
                'windspeed' => $windspeed,
                'user_id' => auth()->user()->id,
                'provinsi' => $provinsi, // Set the value for "provinsi" here
                'kabupaten' => $kabupaten,
            ]);

            // Alternatively, you can perform any other data processing as needed
        }
    }
}
