@extends('layouts.main')

@section('container')
    <form action="{{ route('maps') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi">
                    <option value="" @if(!$selectedProvinsi) selected @endif>Semua Provinsi</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}" @if($selectedProvinsi == $provinsi->id) selected @endif>
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
                    <option value="" @if(!$selectedKabupaten) selected @endif>-- Kabupaten/Kota --</option>
                    @if($selectedProvinsi)
                        @foreach ($provinces->find($selectedProvinsi)->regencies ?? [] as $kabupaten)
                            <option value="{{ $kabupaten->id }}" @if($selectedKabupaten == $kabupaten->id) selected @endif>
                                {{ $kabupaten->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('kabupaten')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <button id="queryButton" class="btn btn-primary mt-3">Query</button>
    </form>

    <div id="map"></div>

    <style>
        #map { height: 500px; }
    </style>
<script>
    var map = L.map('map').setView([-1.269160, 117.825264], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    // Function to handle click events on the map
    function onMapClick(e) {
        // Perform reverse geocoding to get the province/regency name based on the clicked coordinates
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
            .then(response => response.json())
            .then(data => {
                var address = data.address;
                var province = address.state || address.city; // Check for province/state/region
                var regency = address.city || address.town || address.county || address.village; // Check for city, town, or village for regency
                var popupContent = "<strong>Province: </strong>" + province + "<br>" +
                                   "<strong>Regency: </strong> " + regency + "<br>" +
                                   "<a href='https://www.google.com/maps?q=" + e.latlng.lat + "," + e.latlng.lng + "' target='_blank'>See on Google Maps</a>";

                var popup = L.popup()
                    .setLatLng(e.latlng)
                    .setContent(popupContent)
                    .openOn(map);
            })
            .catch(error => console.error('Error:', error));
    }

    // Adding click event listener to the map
    map.on('click', onMapClick);

    // Your existing code to fetch and display GeoJSON data
    var selectedProvinsi = "{{ $selectedProvinsi }}";
    var selectedKabupaten = "{{ $selectedKabupaten }}"; // Ambil ID kabupaten jika tersedia

    var geojsonProvinsiPath = "";
    var geojsonKabupatenPath = "";

    if (selectedProvinsi === "") {
        geojsonProvinsiPath = "/geojson/alldata.geojson"; // If "Semua Provinsi" is selected
    } else {
        if (selectedProvinsi) {
            geojsonProvinsiPath = "/geojson/provinces/" + selectedProvinsi + ".geojson";
        }

        if (selectedKabupaten && selectedKabupaten !== "-- Kabupaten/Kota --") {
            var regencyId = selectedKabupaten.substr(2); 
            geojsonKabupatenPath = "/geojson/regencies/" + selectedProvinsi + "." + regencyId + ".geojson";
        }
    }

    if (geojsonProvinsiPath) {
        fetch(geojsonProvinsiPath)
            .then(res => res.json())
            .then(data => {
                // Add GeoJSON layer for provinsi to map with blue color
                var geojsonLayer = L.geoJson(data, {
                    style: {
                        weight: 1
                    }
                }).addTo(map);
                
                // Fit map to the bounds of the GeoJSON layer
                map.fitBounds(geojsonLayer.getBounds());
            });
    }

    if (geojsonKabupatenPath) {
        fetch(geojsonKabupatenPath)
            .then(res => res.json())
            .then(data => {
                // Add GeoJSON layer for kabupaten to map with red color
                var geojsonLayer = L.geoJson(data, {
                    style: {
                        color: "red",
                        weight: 1
                    }
                }).addTo(map);
                
                // Fit map to the bounds of the GeoJSON layer
                map.fitBounds(geojsonLayer.getBounds());
            });
    }
</script>






@endsection
