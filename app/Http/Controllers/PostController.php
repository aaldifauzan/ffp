<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
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
        $selectedKabupaten = $request->query('kabupaten'); // Tetapkan nilai kabupaten, mungkin null jika tidak ada pilihan kabupaten
        
        // Fetch data based on selected filters
        $postsQuery = Post::query();
        if ($selectedProvinsi) {
            $postsQuery->where('provinsi', $selectedProvinsi);
        }
        
        // Get posts data
        $posts = $postsQuery->get();
    
        // Define the title
        $title = 'Maps';
    
        // Define the active page
        $active = 'maps';
    
        // Set default values for start_date and end_date
        $startDate = $request->query('start_date', now()->subMonth()->format('Y-m-d')); // Default to one month ago
        $endDate = $request->query('end_date', now()->format('Y-m-d')); // Default to today
    
        return view('maps', compact('provinces', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten', 'startDate', 'endDate')); // Sertakan $startDate dan $endDate di sini
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
