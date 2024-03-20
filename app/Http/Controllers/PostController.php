<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Charts\WeatherChart;

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
    
        return view('maps', compact('provinces', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten')); // Sertakan $selectedKabupaten di sini
    }


    public function history(Request $request, WeatherChart $weatherChart)
    {
        $provinces = Province::all();
        $selectedProvinsi = $request->query('provinsi');
        $selectedKabupaten = $request->query('kabupaten');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Fetch data based on selected filters
        $postsQuery = Post::query();
        if ($selectedProvinsi) {
            $postsQuery->where('provinsi', $selectedProvinsi);
        }
        if ($selectedKabupaten) {
            $postsQuery->where('kabupaten', $selectedKabupaten);
        }
        
        // Limit data to the specified date range
        if ($startDate && $endDate) {
            $postsQuery->whereBetween('date', [$startDate, $endDate]);
        }

        // Get posts data
        $posts = $postsQuery->get();

        // If no data found, set error message in session
        if ($posts->isEmpty()) {
            return redirect()->back()->with('error', 'No data found matching the provided filters.');
        }
        
        // Extract data for windspeed chart
        $windspeedChartData = $posts->pluck('windspeed')->toArray();
        $chartLabels = $posts->pluck('date')->toArray();

        // Extract data for humidity chart
        $humidityChartData = $posts->pluck('humidity')->toArray();

        // Extract data for rainfall chart
        $rainfallChartData = $posts->pluck('rainfall')->toArray();

        // Extract data for temperature chart
        $temperatureChartData = $posts->pluck('temperature')->toArray();

        // Build weather charts
        $chart1 = $weatherChart->buildWindspeedChart($windspeedChartData, $chartLabels);
        $chart2 = $weatherChart->buildHumidityChart($humidityChartData, $chartLabels);
        $chart3 = $weatherChart->buildRainfallChart($rainfallChartData, $chartLabels);
        $chart4 = $weatherChart->buildTemperatureChart($temperatureChartData, $chartLabels); // Build temperature chart

        // Define the title
        $title = 'History';

        // Define the active page
        $active = 'history';

        return view('history', compact('provinces', 'chart1', 'chart2', 'chart3', 'chart4', 'title', 'active', 'selectedProvinsi', 'startDate', 'endDate', 'selectedKabupaten'));
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
