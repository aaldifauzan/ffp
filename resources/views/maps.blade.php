@extends('layouts.main')

@section('container')
    <form action="{{ route('maps') }}" method="GET">
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
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div id="map"></div>

    <style>
        #map { height: 500px; }
    </style>
<script>
    var map = L.map('map').setView([-1.269160, 116.825264], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Get the selected province ID from the form
    var selectedProvinsi = "{{ $selectedProvinsi }}";
    var selectedKabupaten = "{{ $selectedKabupaten }}"; // Ambil ID kabupaten jika tersedia

    // Tentukan jalur geojson berdasarkan input pengguna
    var geojsonPath = "";

    if (selectedProvinsi) {
        geojsonPath = "/geojson/provinces/" + selectedProvinsi + ".geojson";
    }

    if (selectedKabupaten && selectedKabupaten !== "-- Kabupaten/Kota --") {
        // Ambil dua digit pertama dari ID kabupaten
        var regencyId = selectedKabupaten.substr(2); 

        // Gabungkan ID provinsi dan ID kabupaten untuk membentuk path yang sesuai
        geojsonPath = "/geojson/regencies/" + selectedProvinsi + "." + regencyId + ".geojson";
    }

    if (geojsonPath) {
        // Fetch the corresponding geojson file based on the selected province or province and kabupaten
        fetch(geojsonPath)
            .then(res => res.json())
            .then(data => {
                L.geoJson(data).addTo(map);
            });
    }
</script>



@endsection
