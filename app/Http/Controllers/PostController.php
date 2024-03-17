<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Charts\WindspeedChart;



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

    public function history(WindspeedChart $chart)
    {
        $title = '';
        return view('history',[
            "title" => $title,
            "active" => 'history',
            'chart' => $chart->build(),
        ]);
    }

    public function show(Post $post){
        return view('post',[
            "title" => "Single Post",
            "active" => 'posts',
            "post" => $post
        ]);
    }
}
