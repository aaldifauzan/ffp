@extends('layouts.main')

@section('container')
    <h1>Halaman Home</h1>

    <div class="form-group">
      <label for="exampleFormControlSelect1">Provinsi</label>
      <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required>
          <option value="" @if(old('provinsi') == '') selected @endif>-- Provinsi --</option>
          @foreach ($provinces as $provinsi)
              <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id) selected @endif>
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
      <label for="exampleFormControlSelect2">Kabupaten/Kota</label>
      <select class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten">

      </select>
  </div>
    
<div class="container px-4 mx-auto">

  <div class="p-6 m-20 bg-white rounded shadow">
      {!! $chart->container() !!}
  </div>

</div>

<script src="{{ $chart->cdn() }}"></script>

{{ $chart->script() }}
@endsection