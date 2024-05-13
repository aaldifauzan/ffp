<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostPredict;
use App\Models\Fwi;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Imports\ProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

use App\Models\Province;
use App\Models\Regency;

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
    $provinces = Province::all();

    // Pass the $provinces variable to the view
    return view('dashboard.posts.importcsv', compact('provinces'));
}
public function handleCSVImport(Request $request)
{
    $validatedData = $request->validate([
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    // Get the uploaded file
    $file = $request->file('csv_file');

    // Provide an explicit type for the Excel import
    $type = 'csv';

    // Use the import method with the provided type
    Excel::import(new ProvinceImport(), $file, $type);

    // You can add your CSV processing logic here based on the uploaded file
    // For demonstration purposes, I'm just returning a success message
    return redirect()->route('dashboard.posts.index')->with('success', 'CSV file has been imported successfully.');
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
        $validatedData = $request->validate([
            'date' => [
                'required',
                'date_format:d-m-Y',
                Rule::unique('posts')->where(function ($query) use ($request) {
                    return $query->where('date', date('Y-m-d', strtotime($request->date)))
                                 ->where('provinsi', $request->provinsi)
                                 ->where('kabupaten', $request->kabupaten);
                }),
            ],
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
        $postsQuery1 = Post::where('provinsi', $provinceId)->where('kabupaten', $regencyId);
        $postsQuery2 = PostPredict::where('provinsi', $provinceId)->where('kabupaten', $regencyId);
        $postsQuery3 = Fwi::where('provinsi', $provinceId)->where('kabupaten', $regencyId);
    
        // Get the selected year from the request or use the current year as default
        $selectedYear = request('year', date('Y'));
    
        if ($selectedYear) {
            $postsQuery1->whereYear('date', '=', $selectedYear);
            $postsQuery2->whereYear('date', '=', $selectedYear);
            $postsQuery3->whereYear('date', '=', $selectedYear);
        }
    
        // Get the filtered posts
        $posts1 = $postsQuery1->get();
        $posts2 = $postsQuery2->get();
        $posts3 = $postsQuery3->get();
    
        // Check if there are any posts
        if ($posts1->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the specified province, regency, and year.');
            // You can customize this error message as needed
        }
    
        return view('dashboard.posts.show', [
            'province' => $province,
            'regency' => $regency,
            'posts1' => $posts1,
            'posts2' => $posts2,
            'posts3' => $posts3,
            'selectedYear' => $selectedYear, // Add this line
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


