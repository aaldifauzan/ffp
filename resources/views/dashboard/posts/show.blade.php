@extends('dashboard.layouts.main')

@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Details for {{ $province->name }} - {{ $regency->name }}</h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger col-lg-8" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success col-lg-8" role="alert">
        {{ session('success') }}
    </div>
@endif

    <div class="col-lg-8">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Temperature</th>
                        <th scope="col">Humidity</th>
                        <th scope="col">Rainfall</th>
                        <th scope="col">Windspeed</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts->sortByDesc('date') as $post)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $post->date }}</td>
                            <td>{{ $post->temperature }}</td>
                            <td>{{ $post->humidity }}</td>
                            <td>{{ $post->rainfall }}</td>
                            <td>{{ $post->windspeed }}</td>
                            <td>
                                <a href="{{ route('dashboard.posts.edit', ['province_id' => $province->id, 'regency_id' => $regency->id, 'post_id' => $post->id]) }}" class="badge bg-warning">
                                    <span data-feather="edit"></span>
                                </a>
                                
                                <form action="/dashboard/posts/{{ $province->id }}/{{ $regency->id }}" method="POST" class="d-inline">
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