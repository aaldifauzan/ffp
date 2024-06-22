@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Daftar Provinsi</h1>
</div>

@if(session('error'))
<div class="alert alert-danger" role="alert">
    {{ session('error') }}
</div>
@endif

@if(session()->has('success'))
<div class="alert alert-success" role="alert">
  {{ session('success') }}
</div>
@endif

<div class="mb-3">
  <a href="/dashboard/posts/create" class="btn btn-primary me-2">Input Data Harian</a>
  <a href="{{ route('dashboard.posts.importcsv') }}" class="btn btn-success">Import CSV</a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
      <thead>
        <tr>
          <th scope="col" class="text-center" style="width: 5%;">No.</th>
          <th scope="col">Provinsi</th>
          <th scope="col" class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($provinces as $provinsi)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $provinsi->name }}</td>
            <td class="text-center">
              <a href="{{ route('dashboard.posts.showRegenciesByProvince', $provinsi->id) }}" class="badge bg-info">
                <span data-feather="eye"></span>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
</div>
@endsection
