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
    <div class="mb-3 row">
      <div class="col-md-6">
            <div class="form-group">
                <label for="provinsi">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi">
                  <option value="" {{ empty($selectedProvince) ? 'selected' : '' }}>-- All Provinsi --</option>
                  @foreach ($provinces as $provinsi)
                      <option value="{{ $provinsi->id }}" {{ $selectedProvince == $provinsi->id ? 'selected' : '' }}>
                          {{ $provinsi->name }}
                      </option>
                  @endforeach
                  
                </select>
                @error('provinsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
              <label for="kabupaten">Kabupaten/Kota</label>
              <select class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten">
                <option value="" {{ empty($selectedRegency) ? 'selected' : '' }}>-- Kabupaten/Kota --</option>
                @foreach ($regencies as $kabupaten)
                    <option value="{{ $kabupaten->id }}" {{ $selectedRegency == $kabupaten->id ? 'selected' : '' }}>
                        {{ $kabupaten->name }}
                    </option>
                @endforeach
                
              </select>
              @error('kabupaten')
                  <div class="invalid-feedback">
                      {{ $message }}
                  </div>
              @enderror
          </div>
      </div>
      <div class="col-md-6 d-flex justify-content-end">
          <button type="submit" class="btn btn-outline-secondary">Filter</button>
      </div>
  </div>
</form>



  

  <div class="table-responsive">
    <table class="table table-striped table-sm">
      <thead>
        <tr>
          <th scope="col">#</th>
          {{-- <th scope="col">Title</th> --}}
          <th scope="col">Provinsi</th>
          <th scope="col">Kota</th>
          <th scope="col">Date</th>
          <th scope="col">Temperature</th>
          <th scope="col">Humidity</th>
          <th scope="col">Rainfall</th>
          <th scope="col">Windspeed</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($posts as $post)
          <tr>
            <td>{{ $loop->iteration }}</td>
            {{-- <td>{{ $post->title }}</td> --}}
            {{-- <td>{{ $post->category->name }}</td> --}}
            <td>{{ $post->province->name }}</td>
            <td>{{ $post->regency->name }}</td>
            <td>{{ $post->date }}</td>
            <td>{{ $post->temperature }}</td>
            <td>{{ $post->humidity }}</td>
            <td>{{ $post->rainfall }}</td>
            <td>{{ $post->windspeed }}</td>
            <td>
              <a href="/dashboard/posts/{{ $post->slug }}" class="badge bg-info"><span data-feather="eye"></span></a>
              <a href="/dashboard/posts/{{ $post->slug }}/edit" class="badge bg-warning"><span data-feather="edit"></span></a>
              <form action="/dashboard/posts/{{ $post->slug }}" method="POST" class="d-inline">
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
