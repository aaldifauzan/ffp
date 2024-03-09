@extends('dashboard.layouts.main')

@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Regencies in {{ $province->name }}</h1>
    </div>

    @if(session('error'))
    <div class="alert alert-danger col-lg-8" role="alert">
        {{ session('error') }}
    </div>
@endif

    <div class="col-lg-8">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Regency/Kota</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($regencies as $regency)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $regency->name }}</td>
                        <!-- Add other columns as needed -->
                        <td>
                            <a href="{{ route('dashboard.posts.show', ['province_id' => $province->id, 'regency_id' => $regency->id]) }}" class="badge bg-info">
                                <span data-feather="eye"></span>
                            </a>

                            <form action="/dashboard/posts/" method="POST" class="d-inline">
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
    </div>
@endsection
