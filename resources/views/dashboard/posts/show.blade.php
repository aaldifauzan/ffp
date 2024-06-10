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
    <div>
        <a href="/dashboard/posts/create" class="btn btn-primary me-2">Input Data Harian</a>
        <a href="{{ route('dashboard.posts.importcsv') }}" class="btn btn-success me-2">Import CSV</a>
        <form action="{{ route('train') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="provinsi" value="{{ $province->id }}">
            <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-warning">Predict</button>
        </form>

        <form action="{{ route('forecast') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="provinsi" value="{{ $province->id }}">
            <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-warning">Forecast</button>
        </form>
    </div>
    <div>
        {{ $posts1->links() }}
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
                <th scope="col">Windspeed Predict</th>
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
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $post1->date }}</td>
                    <td>{{ $post1->temperature ?? '-' }}</td>
                    <td>{{ $postPredict ? round($postPredict->temperature_predict, 1) : '-' }}</td>
                    <td>{{ $post1->humidity ?? '-' }}</td>
                    <td>{{ $postPredict ? round($postPredict->humidity_predict, 1) : '-' }}</td>
                    <td>{{ isset($post1->rainfall) ? round($post1->rainfall, 1) : '-' }}</td>
                    <td>{{ $postPredict ? round($postPredict->rainfall_predict, 1) : '-' }}</td>
                    <td>{{ $post1->windspeed ?? '-' }}</td>
                    <td>{{ $postPredict ? round($postPredict->windspeed_predict, 1) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->ffmc, 1) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->dmc, 1) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->dc, 1) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->isi, 3) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->bui, 1) : '-' }}</td>
                    <td>{{ $fwi ? number_format($fwi->fwi, 5) : '-' }}</td>
                    <td>
                        <a href="{{ route('dashboard.posts.edit', ['province_id' => $province->id, 'regency_id' => $regency->id, 'post_id' => $post1->id]) }}" class="badge bg-warning">
                            <span data-feather="edit"></span>
                        </a>
                        <form action="{{ route('dashboard.posts.destroy', ['date' => $post1->date, 'provinsi' => $post1->provinsi, 'kabupaten' => $post1->kabupaten]) }}" method="POST" class="d-inline">
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

{{ $posts1->appends(['province' => $province->id, 'regency' => $regency->id])->links() }}
@endsection
