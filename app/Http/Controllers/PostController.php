<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Charts\WeatherChart;
use GuzzleHttp\Client;
use App\Models\PostPredict;

use App\Models\Province;
use App\Models\Regency;

use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        $title = '';
        if(request('category')){
            $category = Category::firstWhere('slug', request('category'));
            $title = ' in ' . $category->name;
        }

        if(request('author')){
            $author = User::firstWhere('username', request('author'));
            $title =  ' by ' . $author->name;
        }
    }

    public function home()
    {
        return view('home', [
            "title" => "Home",
            "active" => 'home',
        ]);
    }

    // public function maps()
    // {
    //     return view('maps',[
    //         "title" => "Maps",
    //         "active" => 'maps'
    //     ]);
    // }

    public function maps(Request $request)
    {
        $provinces = Province::all();
        $selectedProvinsi = $request->query('provinsi');
        $selectedKabupaten = $request->query('kabupaten');
        
        $postsQuery = Post::query();
        if ($selectedProvinsi) {
            $postsQuery->where('provinsi', $selectedProvinsi);
        }
        $posts = $postsQuery->get();
    
        $title = 'Maps';
        $active = 'maps';
    
        $startDate = $request->query('start_date', now()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->addDays(7)->format('Y-m-d'));        
    
        $geojsonData = $this->loadGeojsonData($selectedProvinsi, $selectedKabupaten);
        $colorMapping = [];
    
        // Fetch initial FWI data for the current date
        $currentFWIResponse = Http::post('http://127.0.0.1:5000/api/fwi-data-map', [
            'date' => now()->format('Y-m-d'),
        ]);
    
        if ($currentFWIResponse->successful()) {
            $fwiData = $currentFWIResponse->json();
            foreach ($fwiData as $entry) {
                $alt_name = $entry['name'];
                $fwi = $entry['FWI'];
    
                if ($fwi < 1) {
                    $colorMapping[$alt_name] = '#0E7AD1';
                } elseif ($fwi < 6) {
                    $colorMapping[$alt_name] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorMapping[$alt_name] = '#FFFF00';
                } else {
                    $colorMapping[$alt_name] = '#FF0000';
                }
            }
        }
    
        return view('maps', compact('provinces', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten', 'startDate', 'endDate', 'geojsonData', 'colorMapping'));
    }
    

    private function loadGeojsonData($selectedProvinsi, $selectedKabupaten)
    {
        $filePath = public_path('geojson/Full/alldata.geojson');

        if ($selectedProvinsi) {
            $filePath = public_path("geojson/provinces/{$selectedProvinsi}.geojson");
        }
        if ($selectedKabupaten) {
            $regencyId = substr($selectedKabupaten, 2);
            $filePath = public_path("geojson/regencies/{$selectedProvinsi}.{$regencyId}.geojson");
        }

        if (File::exists($filePath)) {
            $contents = File::get($filePath);
            return json_decode($contents, true);
        }

        return [];
    }

    public function getFWIDataRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedProvinsi = $request->input('provinsi');
        $selectedKabupaten = $request->input('kabupaten');

        $response = Http::post('http://127.0.0.1:5000/api/fwi-data-all', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'provinsi' => $selectedProvinsi,
            'kabupaten' => $selectedKabupaten,
        ]);

        if ($response->successful()) {
            $fwiData = $response->json();

            return response()->json([
                'status' => 'success',
                'data' => $fwiData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found or an error occurred.'
            ]);
        }
    }

    public function getFWIDataCurrent(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));  // Default ke hari ini jika tanggal tidak disediakan
    
        $response = Http::post('http://127.0.0.1:5000/api/fwi-data-current', [
            'date' => $date,
        ]);
    
        if ($response->successful()) {
            $fwiData = $response->json();
            $colorMapping = [];
            $colorid = [];  // Tambahkan inisialisasi untuk colorid
    
            foreach ($fwiData as $entry) {
                $alt_name = $entry['name'];
                $id = $entry['kabupaten_id'];
                $fwi = $entry['FWI'];
    
                if ($fwi < 1) {
                    $colorMapping[$alt_name] = '#0E7AD1';
                } elseif ($fwi < 6) {
                    $colorMapping[$alt_name] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorMapping[$alt_name] = '#FFFF00';
                } else {
                    $colorMapping[$alt_name] = '#FF0000';
                }
    
                if ($fwi < 1) {
                    $colorid[$id] = '#ADD8E6';
                } elseif ($fwi < 6) {
                    $colorid[$id] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorid[$id] = '#FFFF00';
                } else {
                    $colorid[$id] = '#FF0000';
                }
            }
    
            return response()->json([
                'status' => 'success',
                'colorMapping' => $colorMapping,
                'colorKabupaten' => $colorid,
                'data' => $fwiData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found or an error occurred.'
            ]);
        }
    }
    
    public function getFWIHistoryData(Request $request)
{
    $selectedProvinsi = $request->input('provinsi');
    $selectedKabupaten = $request->input('kabupaten');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $response = Http::post('http://127.0.0.1:5000/api/fwi-data-all', [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'provinsi' => $selectedProvinsi,
        'kabupaten' => $selectedKabupaten,
    ]);

    if ($response->successful()) {
        return response()->json([
            'status' => 'success',
            'data' => $response->json()
            
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'No data found or an error occurred.'
        ]);
    }
}

    


    public function history(Request $request, WeatherChart $weatherChart)
    {
        $provinces = Province::all();
        $selectedProvinsi = $request->query('provinsi');
        $selectedKabupaten = $request->query('kabupaten');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Fetch actual data
        $postsQuery = Post::query();
        if ($selectedProvinsi) {
            $postsQuery->where('provinsi', $selectedProvinsi);
        }
        if ($selectedKabupaten) {
            $postsQuery->where('kabupaten', $selectedKabupaten);
        }
        if ($startDate && $endDate) {
            $postsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $posts = $postsQuery->get();

        // Fetch predicted data
        $postPredictsQuery = PostPredict::query();
        if ($selectedProvinsi) {
            $postPredictsQuery->where('provinsi', $selectedProvinsi);
        }
        if ($selectedKabupaten) {
            $postPredictsQuery->where('kabupaten', $selectedKabupaten);
        }
        if ($startDate && $endDate) {
            $postPredictsQuery->whereBetween('date', [$startDate, $endDate]);
        }
        $postPredicts = $postPredictsQuery->get();

        // If no data found, set error message in session
        if ($posts->isEmpty() || $postPredicts->isEmpty()) {
            return redirect()->back()->with('error', 'No data found matching the provided filters.');
        }

        // Extract data for charts
        $chartLabels = $posts->pluck('date')->toArray();

        // Actual data
        $temperatureData = $posts->pluck('temperature')->toArray();
        $humidityData = $posts->pluck('humidity')->toArray();
        $rainfallData = $posts->pluck('rainfall')->toArray();
        $windspeedData = $posts->pluck('windspeed')->toArray();

        // Predicted data
        $temperaturePredictData = $postPredicts->pluck('temperature_predict')->toArray();
        $humidityPredictData = $postPredicts->pluck('humidity_predict')->toArray();
        $rainfallPredictData = $postPredicts->pluck('rainfall_predict')->toArray();
        $windspeedPredictData = $postPredicts->pluck('windspeed_predict')->toArray();

        // Build weather charts
        $temperatureChart = $weatherChart->buildWeatherChart($temperatureData, $temperaturePredictData, $chartLabels, 'Temperature');
        $humidityChart = $weatherChart->buildWeatherChart($humidityData, $humidityPredictData, $chartLabels, 'Humidity');
        $rainfallChart = $weatherChart->buildWeatherChart($rainfallData, $rainfallPredictData, $chartLabels, 'Rainfall');
        $windspeedChart = $weatherChart->buildWeatherChart($windspeedData, $windspeedPredictData, $chartLabels, 'Windspeed');

        $title = 'History';
        $active = 'history';

        return view('history', compact('provinces', 'temperatureChart', 'humidityChart', 'rainfallChart', 'windspeedChart', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten', 'startDate', 'endDate'));
    }
    


    public function getkota(request $request)
    {
        $id_provinsi = $request->id_provinsi;

        $kabupatens = Regency::where('province_id', $id_provinsi)->get();

        $option = "<option>-- Kabupaten/Kota --</option>";
        foreach($kabupatens as $kabupaten){
            $option.= "<option value='$kabupaten->id'>$kabupaten->name</option>";
        }
        echo $option;
    }

}
