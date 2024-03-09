<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

class DashboardPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $provinceId = $request->input('provinsi');
    $regencyId = $request->input('kabupaten');
    
    $postsQuery = Post::where('user_id', auth()->user()->id);

    if ($provinceId) {
        $postsQuery->where('provinsi', $provinceId);
    }

    if ($regencyId && $regencyId != "-- Kabupaten/Kota --") {
        $postsQuery->where('kabupaten', $regencyId);
    }


    $posts = $postsQuery->get();
    
    return view('dashboard.posts.index', [
        'posts' => $posts,
        'provinces' => Province::all(),
        'regencies' => Regency::all(),
        'selectedProvince' => $provinceId,
        'selectedRegency' => $regencyId,
    ]);
}

public function importCSV()
{
    // Add logic for CSV import page
    return view('dashboard.posts.importcsv');
}

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces = Province::all();
        $categories = Category::all();

        return view('dashboard.posts.create', compact('provinces','categories'));
    }

    public function getkabupaten(request $request)
    {
        $id_provinsi = $request->id_provinsi;

        $kabupatens = Regency::where('province_id', $id_provinsi)->get();

        $option = "<option>-- Kabupaten/Kota --</option>";
        foreach($kabupatens as $kabupaten){
            $option.= "<option value='$kabupaten->id'>$kabupaten->name</option>";
        }
        echo $option;
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request-> validate([
            'date' => 'required|date_format:d-m-Y',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'temperature' => 'required',
            'humidity' => 'required',
            'rainfall' => 'required',
            'windspeed' => 'required',
        ]);

        $validatedData['user_id'] = auth()->user()->id;

        Post::create($validatedData);

        return redirect('/dashboard/posts')->with('success', 'New post has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show($provinceId, $regencyId)
    {
        $province = Province::find($provinceId);
        $regency = Regency::find($regencyId);
    
        if (!$province || !$regency) {
            abort(404); // Handle the case when either the province or regency is not found
        }
    
        // Fetch all posts based on the province and regency
        $posts = Post::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->get();
    
        // Check if there are any posts
        if ($posts->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the specified province and regency.');
            // You can customize this error message as needed
        }
    
        return view('dashboard.posts.show', [
            'province' => $province,
            'regency' => $regency,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($provinceId, $regencyId, $postId)
    {
        // Retrieve the province and regency based on IDs
        $province = Province::find($provinceId);
        $regency = Regency::find($regencyId);
        
        // Check if both province and regency exist
        if (!$province || !$regency) {
            abort(404); // Handle the case when either the province or regency is not found
        }
        
        // Fetch the post based on the post ID
        $post = Post::find($postId);
        
        // Fetch all provinces and regencies
        $provinces = Province::all();
        $regencies = Regency::all();
        
        // Check if the post exists
        if (!$post) {
            return redirect()->back()->with('error', 'No data found for the specified post.');
            // You can customize this error message as needed
        }
        
        return view('dashboard.posts.edit', [
            'province' => $province,
            'regency' => $regency,
            'post' => $post,
            'provinces' => $provinces,
            'regencies' => $regencies, // Ensure that the variable is passed correctly
            'categories' => Category::all(),
            // Add other data you may need for the edit view
        ]);
    }
    
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $rules = [
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'temperature' => 'required',
            'humidity' => 'required',
            'rainfall' => 'required',
            'windspeed' => 'required',
        ];
    
        $validatedData = $request->validate($rules);
    
        $validatedData['user_id'] = auth()->user()->id;
    
        // Update the post based on the model
        $post->update($validatedData);
    
        return redirect()->route('dashboard.posts.show', ['province_id' => $post->provinsi, 'regency_id' => $post->kabupaten])
        ->with('success', 'Post has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
    
        return redirect()->back()->with('success', 'Post has been deleted!');
    }


    public function showRegenciesByProvince($provinceId)
    {
        $province = Province::find($provinceId);
    
        if (!$province) {
            abort(404); // Handle the case when the province is not found
        }
    
        $regencies = Regency::where('province_id', $provinceId)->get();
    
        return view('dashboard.posts.index_regency', [
            'province' => $province,
            'regencies' => $regencies,
        ]);
    }
}


