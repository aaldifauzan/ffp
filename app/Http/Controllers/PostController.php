<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Charts\WindspeedChart;

use App\Models\Province;
use App\Models\Regency;

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
            "title" => "About",
            "name" => "Aldi Fauzan",
            "email" => "aldifauzaan@student.telkomuniversity.ac.id",
            "image" => "foto.jpg",
            "active" => 'maps'
        ]);
    }


    public function history(WindspeedChart $chart)
    {
        $provinces = Province::all();
        
        return view('history', compact('provinces'))->with([
            "title" => 'History',
            "active" => 'history',
            'chart' => $chart->build(),
        ]);
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



    public function show(Post $post){
        return view('post',[
            "title" => "Single Post",
            "active" => 'posts',
            "post" => $post
        ]);
    }
}
