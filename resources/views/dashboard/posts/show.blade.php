@extends('dashboard.layouts.main')

@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Detail Data di {{ $province->name }} - {{ $regency->name }}</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <div class="d-flex">
                <a href="/dashboard/posts/create" class="btn btn-primary me-2">Input Data Harian</a>
                <a href="{{ route('dashboard.posts.importcsv') }}" class="btn btn-success me-2">Import CSV</a>
            </div>
            <form action="{{ route('dashboard.posts.show', ['province_id' => $province->id, 'regency_id' => $regency->id]) }}" method="GET" class="d-flex w-25">
                <select name="year" id="year" class="form-select me-1">
                    @foreach(range(date("Y"), 2018, -1) as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Date</th>
                    <th scope="col">Temperature</th>
                    <th scope="col">Temperature Predict</th> 
                    <th scope="col">Humidity</th>
                    <th scope="col">Humidity Predict</th>
                    <th scope="col">Rainfall</th>
                    <th scope="col">Rainfall Predict</th>
                    <th scope="col">Windspeed</th>
                    <th scope="col">FFMC</th>
                    <th scope="col">DMC</th>
                    <th scope="col">DC</th>
                    <th scope="col">ISI</th>
                    <th scope="col">BUI</th>
                    <th scope="col">FWI</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts1->sortByDesc('date') as $post1)
                    @php
                        // Cari data PostPredict yang sesuai dengan tanggal, provinsi, dan kabupaten dari Post
                        $postPredict = $posts2->where('date', $post1->date)
                                              ->where('provinsi', $post1->provinsi)
                                              ->where('kabupaten', $post1->kabupaten)
                                              ->first();
                        // Cari data Fwi yang sesuai dengan tanggal, provinsi, dan kabupaten dari Post
                        $fwi = $posts3->where('date', $post1->date)
                                      ->where('provinsi', $post1->provinsi)
                                      ->where('kabupaten', $post1->kabupaten)
                                      ->first();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $post1->date }}</td>
                        <td>{{ $post1->temperature }}</td>
                        <td>{{ $postPredict ? $postPredict->temperature_predict : '-' }}</td>
                        <td>{{ $post1->humidity }}</td>
                        <td>{{ $postPredict ? $postPredict->humidity_predict : '-' }}</td>
                        <td>{{ $post1->rainfall }}</td>
                        <td>{{ $postPredict ? $postPredict->rainfall_predict : '-' }}</td>
                        <td>{{ $post1->windspeed }}</td>
                        <td>{{ $fwi ? $fwi->ffmc : '-' }}</td>
                        <td>{{ $fwi ? $fwi->dmc : '-' }}</td>
                        <td>{{ $fwi ? $fwi->dc : '-' }}</td>
                        <td>{{ $fwi ? $fwi->isi : '-' }}</td>
                        <td>{{ $fwi ? $fwi->bui : '-' }}</td>
                        <td>{{ $fwi ? $fwi->fwi : '-' }}</td>
                        <td>
                            <a href="{{ route('dashboard.posts.edit', ['province_id' => $province->id, 'regency_id' => $regency->id, 'post_id' => $post1->id]) }}" class="badge bg-warning">
                                <span data-feather="edit"></span>
                            </a>
                            
                            <form action="{{ route('dashboard.posts.destroy', ['post' => $post1->id]) }}" method="POST" class="d-inline">
                                @method('delete')
                                @csrf
                                <button class="badge bg-danger border-0" onclick="return confirm('Are you sure?')">
                                    <span data-feather="x-circle"></span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
