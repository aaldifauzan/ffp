@extends('dashboard.layouts.main')

@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Kabupaten/Kota di {{ $province->name }}</h1>
    </div>

    @if(session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
    @endif
    <div class="">
        <a href="/dashboard/posts/create" class="btn btn-primary mb-3">Input Data Harian</a>
        <a href="{{ route('dashboard.posts.importcsv') }}" class="btn btn-success mb-3">Import CSV</a>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Kabupaten/Kota</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($regencies as $regency)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $regency->name }}</td>
                        <td>
                            <a href="{{ route('dashboard.posts.show', ['province' => $province->id, 'regency' => $regency->id]) }}" class="badge bg-info">
                                <span data-feather="eye"></span>
                            </a>


                            <form action="/dashboard/posts/" method="POST" class="d-inline">
                              @method('delete')
                              @csrf
                            </form>
                          </td>
                    </tr>
                    
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
