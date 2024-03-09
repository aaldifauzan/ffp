@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Posts</h1>
</div>

@if(session()->has('success'))
<div class="alert alert-success col-lg-8" role="alert">
  {{ session('success') }}
</div>
@endif

<div class="col-lg-8">
  <a href="/dashboard/posts/create" class="btn btn-primary mb-3">Create new post</a>
  <form action="/dashboard/posts" method="GET" class="mb-3">
  </form>
  <div class="table-responsive">
    <table class="table table-striped table-sm">
      <thead>
        <tr>
          <th scope="col">No.</th>
          <th scope="col">Provinsi</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($provinces as $provinsi)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $provinsi->name }}</td>
            <td>
              <a href="{{ route('dashboard.posts.showRegenciesByProvince', $provinsi->id) }}" class="badge bg-info">
                <span data-feather="eye"></span>
              </a>
              <form action="/dashboard/posts/{{ $provinsi->id }}" method="POST" class="d-inline">
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
