<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Charts\WindspeedChart;

use App\Models\Province;
use App\Models\Regency;

use Carbon\Carbon;

class PostController extends Controller
{
    public function index(WindspeedChart $chart)
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

    public function maps()
    {
        return view('maps',[
            "title" => "Maps",
            "active" => 'maps'
        ]);
    }



    public function history(Request $request, WindspeedChart $chart1)
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
        
        // Extract windspeed and date data for chart
        $chartData = $posts->pluck('windspeed')->toArray();
        $chartLabels = $posts->pluck('date')->toArray();
    
        // Build the chart
        $chart1 = $chart1->build($chartData, $chartLabels);
    
        // Define the title
        $title = 'History';
    
        // Define the active page
        $active = 'history';
    
        return view('history', compact('provinces', 'chart1', 'title', 'active'));
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
