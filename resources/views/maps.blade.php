@extends('layouts.main')

@section('container')
<div class="container mt-4">
    <form action="{{ route('maps') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required>
                    <option value="" @if(old('provinsi') == '' || !$selectedProvinsi) selected @endif>-- Provinsi --</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id || $selectedProvinsi == $provinsi->id) selected @endif>
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
                        @foreach ($provinces->find($selectedProvinsi)->regencies as $kabupaten)
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

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="start_date">Tanggal Awal:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') ?? now()->format('Y-m-d') }}">
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') ?? now()->addDays(7)->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(30)->format('Y-m-d') }}">
            </div>
        </div>
        
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="predict-tab" data-toggle="tab" href="#predict" role="tab" aria-controls="predict" aria-selected="true">Predict</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="forecast-tab" data-toggle="tab" href="#forecast" role="tab" aria-controls="forecast" aria-selected="false">Forecast</a>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="predict" role="tabpanel" aria-labelledby="predict-tab">
                <div class="form-row mb-3 mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-block" id="predictButton">Predict</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="forecast" role="tabpanel" aria-labelledby="forecast-tab">
                <div class="form-row mb-3 mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-block" id="forecastButton">Forecast</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="map"></div>
    <div id="results"></div>
</div>
<style>
    #map { height: 500px; }
    .legend {
        line-height: 18px;
        color: #555;
        background: white;
        padding: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }
    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }
</style>

<script>
var geojsonLayer;
var geojsonData;
var colorMapping = {}; // Initialize color mapping globally
var savedColorMapping = {}; // Save existing color mapping

var map = L.map('map').setView([-1.269160, 117.825264], 5);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

function onMapClick(e) {
    // Perform reverse geocoding to get the province/regency name based on the clicked coordinates
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + e.latlng.lat + '&lon=' + e.latlng.lng)
        .then(response => response.json())
        .then(data => {
            var address = data.address;
            var province = address.state || address.city;
            var regency = address.city || address.town || address.county || address.village;
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

document.addEventListener('DOMContentLoaded', function() {
    loadMapData();

    // Add event listeners for dropdown changes
    document.getElementById('provinsi').addEventListener('change', function() {
        saveCurrentColorMapping();
        if (this.value) {
            zoomToSelectedArea('provinsi', this.value);
        } else {
            loadAllData();
        }
    });

    document.getElementById('kabupaten').addEventListener('change', function() {
        saveCurrentColorMapping();
        if (this.value) {
            zoomToSelectedArea('kabupaten', this.options[this.selectedIndex].text);
        } else {
            var selectedProvinsi = document.getElementById('provinsi').value;
            if (selectedProvinsi) {
                zoomToSelectedArea('provinsi', selectedProvinsi);
            } else {
                loadAllData();
            }
        }
    });
});

function loadMapData() {
    loadAllData();
}

function loadAllData() {
    var geojsonPath = "/geojson/alldata.geojson";

    fetch(geojsonPath)
        .then(res => res.json())
        .then(data => {
            geojsonData = data;
            if (geojsonLayer) {
                map.removeLayer(geojsonLayer);
            }
            geojsonLayer = L.geoJson(data, {
                style: {
                    weight: 1
                }
            }).addTo(map);

            applyColorMapping(); // Apply color mapping after loading data

            map.fitBounds(geojsonLayer.getBounds());
            fetchFWIDataCurrent();
        });
}

function fetchFWIDataCurrent() {
    fetch('{{ route('fwi-data-current') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ date: new Date().toISOString().slice(0, 10) }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            colorMapping = data.colorMapping; // Update global color mapping
            applyColorMapping();
        } else {
            document.getElementById('results').innerHTML = '<p>' + data.message + '</p>';
        }
    })
    .catch(error => console.error('Error:', error));
}

function saveCurrentColorMapping() {
    savedColorMapping = { ...colorMapping }; // Save the existing color mapping
}

function restoreSavedColorMapping() {
    colorMapping = { ...savedColorMapping }; // Restore the saved color mapping
    applyColorMapping();
}

function applyColorMapping() {
    if (geojsonLayer && colorMapping) {
        geojsonLayer.setStyle(function (feature) {
            var altName = feature.properties.alt_name;

            if (colorMapping.hasOwnProperty(altName)) {
                return {
                    fillColor: colorMapping[altName],
                    weight: 2,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            } else {
                return {
                    fillColor: 'gray',
                    weight: 1,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: 0.7
                };
            }
        });
    }
}

function zoomToSelectedArea(type, id) {
    var filteredData;
    if (type === 'provinsi') {
        filteredData = geojsonData.features.filter(function(feature) {
            return feature.properties.prov_id === id;
        });
    } else if (type === 'kabupaten') {
        filteredData = geojsonData.features.filter(function(feature) {
            return feature.properties.alt_name.toLowerCase() === id.toLowerCase();
        });

        // Check if no Kabupaten is selected (id is empty) and zoom back to province
        if (filteredData.length === 0) {
            var selectedProvinsi = document.getElementById('provinsi').value;
            if (selectedProvinsi) {
                filteredData = geojsonData.features.filter(function(feature) {
                    return feature.properties.prov_id === selectedProvinsi;
                });
            }
        }
    }

    if (geojsonLayer) {
        map.removeLayer(geojsonLayer);
    }

    geojsonLayer = L.geoJson(filteredData, {
        style: {
            weight: 1
        }
    }).addTo(map);

    restoreSavedColorMapping(); // Restore the saved color mapping

    map.fitBounds(geojsonLayer.getBounds());
}

// Add the legend to the map
var legend = L.control({position: 'bottomright'});

legend.onAdd = function (map) {
    var div = L.DomUtil.create('div', 'legend'),
        grades = [0, 1, 6, 13],
        labels = [];

    div.innerHTML = '<strong>FWI Index</strong><br>';
    for (var i = 0; i < grades.length; i++) {
        div.innerHTML +=
            '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
            grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
    }

    return div;
};

legend.addTo(map);

function getColor(d) {
    return d > 13 ? '#FF0000' :
           d > 6  ? '#FFFF00' :
           d > 1  ? '#00FF00' :
                    '#0E7AD1';
}
</script>

@endsection
