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

<div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="btn-group" role="group">
        <form action="/dashboard/posts/create" method="GET" class="d-inline">
            <button type="submit" class="btn btn-primary me-2">Input Data Harian</button>
        </form>

        <form action="{{ route('dashboard.posts.importcsv') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-success me-2">Import CSV</button>
        </form>
    </div>

    <div class="btn-group" role="group">
        <form action="{{ route('train') }}" method="POST" class="d-inline me-2">
            @csrf
            <input type="hidden" name="provinsi" value="{{ $province->id }}">
            <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-danger">Predict</button>
        </form>

        <form action="{{ route('forecast') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="provinsi" value="{{ $province->id }}">
            <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-danger">Forecast</button>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th scope="col" class="text-center px-1" style="width: 4%;">No.</th>
                <th scope="col" class="text-center px-1" style="width: 5%; white-space: nowrap;">Date</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Temperature</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Temperature Predict</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Humidity</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Humidity Predict</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Rainfall</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Rainfall Predict</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Windspeed</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">Windspeed Predict</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">FFMC</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">DMC</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">DC</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">ISI</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">BUI</th>
                <th scope="col" class="text-center px-1" style="width: 3%;">FWI</th>
                <th scope="col" class="text-center px-1" style="width: 8%;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts1 as $post1)
                @php
                    $postPredict = $posts2->where('date', $post1->date)
                                          ->where('provinsi', $post1->provinsi)
                                          ->where('kabupaten', $post1->kabupaten)
                                          ->first();
                    $fwi = $posts3->where('date', $post1->date)
                                  ->where('provinsi', $post1->provinsi)
                                  ->where('kabupaten', $post1->kabupaten)
                                  ->first();
                @endphp
                <tr>
                    <td class="text-center px-1">{{ $loop->iteration }}</td>
                    <td class="text-center px-1" style="white-space: nowrap;">{{ $post1->date }}</td>
                    <td class="text-center px-1">{{ $post1->temperature ?? '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->temperature_predict, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $post1->humidity ?? '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->humidity_predict, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ isset($post1->rainfall) ? round($post1->rainfall, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->rainfall_predict, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $post1->windspeed ?? '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->windspeed_predict, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->ffmc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->dmc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->dc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->isi, 3) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->bui, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->fwi, 5) : '-' }}</td>
                    <td class="text-center px-1">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('dashboard.posts.edit', ['province_id' => $province->id, 'regency_id' => $regency->id, 'date' => $post1->date]) }}" class="badge bg-warning me-1">
                                <span data-feather="edit"></span>
                            </a>
                            <form action="{{ route('dashboard.posts.destroy', ['date' => $post1->date, 'provinsi' => $post1->provinsi, 'kabupaten' => $post1->kabupaten]) }}" method="POST" class="d-inline">
                                @method('delete')
                                @csrf
                                <button class="badge bg-danger border-0" onclick="return confirm('Are you sure?')">
                                    <span data-feather="x-circle"></span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $posts1->links() }}
</div>

@endsection
