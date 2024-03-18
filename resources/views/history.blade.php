@extends('layouts.main')

@section('container')
    <h1>Halaman History</h1>

    @if(session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('history') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
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
            <div class="form-group col-md-6">
                <label for="kabupaten">Kabupaten/Kota</label>
                <select class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten">
                    <option>-- Kabupaten/Kota --</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="start_date">Tanggal Awal:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}">
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    @if(request()->hasAny(['provinsi', 'kabupaten', 'start_date', 'end_date']))
        <div class="container px-4 mx-auto">
            <div class="p-6 m-20 bg-white rounded shadow">
                {!! $chart1->container() !!}
            </div>
        </div>
        <script src="{{ $chart1->cdn() }}"></script>
        {{ $chart1->script() }}
    @endif
@endsection
