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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Pagination\LengthAwarePaginator;


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

    return view('dashboard.posts.importcsv', compact('provinces'));
}
public function handleCSVImport(Request $request)
{
    $validatedData = $request->validate([
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');

    $type = 'csv';

    Excel::import(new ProvinceImport(), $file, $type);

    return redirect()->route('dashboard.posts.index')->with('success', 'CSV file has been imported successfully.');
}

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces = Province::all();
        // $categories = Category::all();

        return view('dashboard.posts.create', compact('provinces'));
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
            'date' => 'required|date_format:d-m-Y',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'temperature' => 'required',
            'humidity' => 'required',
            'rainfall' => 'required',
            'windspeed' => 'required',
        ]);
    
        $validatedData['user_id'] = auth()->user()->id;
        // Use the hidden input for the date formatted correctly for the database
        $validatedData['date'] = date('Y-m-d', strtotime($request->formatted_date));
    
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
            abort(404);
        }
    
        $postsQuery1 = Post::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->orderBy('date', 'desc');
        $postsQuery2 = PostPredict::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->orderBy('date', 'desc');
        $postsQuery3 = Fwi::where('provinsi', $provinceId)->where('kabupaten', $regencyId);
    
        $posts1 = $postsQuery1->get();
        $posts2 = $postsQuery2->get();
        $posts3 = $postsQuery3->get();
    
        // Merge actual and predicted posts, removing duplicates
        $combinedPosts = $posts1->merge($posts2)->unique('date')->sortByDesc('date');
    
        // Paginate the combined result
        $perPage = 50;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $combinedPosts->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedCombinedPosts = new LengthAwarePaginator($currentItems, $combinedPosts->count(), $perPage);
        $paginatedCombinedPosts->setPath(route('dashboard.posts.show', ['province' => $provinceId, 'regency' => $regencyId]));
    
        if ($paginatedCombinedPosts->isEmpty()) {
            return redirect()->back()->with('error', 'No data found for the specified province, regency, and year.');
        }
    
        return view('dashboard.posts.show', [
            'province' => $province,
            'regency' => $regency,
            'posts1' => $paginatedCombinedPosts,
            'posts2' => $posts2,
            'posts3' => $posts3,
        ]);
    }
    
    




    public function train(Request $request)
    {
        $provinceId = $request->input('provinsi');
        $regencyId = $request->input('kabupaten');
    
        \Log::info('Training with Province ID: ' . $provinceId . ' and Regency ID: ' . $regencyId);
    
        // Define the endpoint for the API request
        $endpoint = 'http://127.0.0.1:5000/train';
    
        try {
            // Make the API request to Flask with increased timeout
            $response = Http::timeout(120)->post($endpoint, [
                'selectedProvinsi' => $provinceId,
                'selectedKabupaten' => $regencyId,
            ]);
    
            // Check if the response is successful
            if ($response->successful()) {
                $predictionData = $response->json();
    
                // Handle the prediction data as needed
                foreach ($predictionData as $date => $data) {
                    // Process the data, for example, store it in the database or display it in the view
                }
    
                return redirect()->back()->with('success', 'Prediction completed successfully.');
            } else {
                // Log the error message from the response
                $errorMessage = $response->json()['error'] ?? 'Unknown error';
                \Log::error('Prediction failed: ' . $errorMessage);
                return redirect()->back()->with('error', 'Prediction failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Prediction failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Prediction failed: ' . $e->getMessage());
        }
    }



    public function forecast(Request $request)
    {
        $provinceId = $request->input('provinsi');
        $regencyId = $request->input('kabupaten');
    
        \Log::info('Forecasting with Province ID: ' . $provinceId . ' and Regency ID: ' . $regencyId);
    
        // Define the endpoint for the API request
        $endpoint = 'http://127.0.0.1:8888/forecast';
    
        try {
            // Make the API request to Flask with increased timeout
            $response = Http::timeout(120)->post($endpoint, [
                'selectedProvinsi' => $provinceId,
                'selectedKabupaten' => $regencyId,
            ]);
    
            // Check if the response is successful
            if ($response->successful()) {
                $predictionData = $response->json();
    
                // Handle the prediction data as needed
                foreach ($predictionData as $date => $data) {
                    // Process the data, for example, store it in the database or display it in the view
                }
    
                return redirect()->back()->with('success', 'Forecast completed successfully.');
            } else {
                // Log the error message from the response
                $errorMessage = $response->json()['error'] ?? 'Unknown error';
                \Log::error('Forecast failed: ' . $errorMessage);
                return redirect()->back()->with('error', 'Forecast failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Forecast failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Forecast failed: ' . $e->getMessage());
        }
    }


    public function fwi(Request $request)
    {
        $provinceId = $request->input('provinsi');
        $regencyId = $request->input('kabupaten');
    
        \Log::info('FWI with Province ID: ' . $provinceId . ' and Regency ID: ' . $regencyId);
    
        // Define the endpoint for the API request
        $endpoint = 'http://127.0.0.1:5000/fwi';
    
        try {
            // Make the API request to Flask with increased timeout
            $response = Http::timeout(120)->post($endpoint, [
                'selectedProvinsi' => $provinceId,
                'selectedKabupaten' => $regencyId,
            ]);
    
            // Check if the response is successful
            if ($response->successful()) {
                $predictionData = $response->json();
    
                // Handle the prediction data as needed
                foreach ($predictionData as $date => $data) {
                    // Process the data, for example, store it in the database or display it in the view
                }
    
                return redirect()->back()->with('success', 'FWI completed successfully.');
            } else {
                // Log the error message from the response
                $errorMessage = $response->json()['error'] ?? 'Unknown error';
                \Log::error('Forecast failed: ' . $errorMessage);
                return redirect()->back()->with('error', 'FWI failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Forecast failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'FWI failed: ' . $e->getMessage());
        }
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
            // 'categories' => Category::all(),
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


