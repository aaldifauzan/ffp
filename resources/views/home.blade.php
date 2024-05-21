@extends('layouts.main')

@section('container')
    <div class="home-image-container">
        <img src="{{ asset('img/home.jpg') }}" alt="Forest Fire" class="home-image">
    </div>
    <footer class="home-footer">
        <div class="footer-left">
            <div class="logo-container">
                <img src="{{ asset('img/TelU.png') }}" alt="Logo" class="footer-logo">
            </div>
            <div class="logo-container">
                <img src="{{ asset('img/BMKG.png') }}" alt="Second Logo" class="footer-logo">
            </div>
        </div>
        <div class="footer-right">
            <p><strong>Contact Us:</strong> <br> Forest Fire Prediction <br> Telp: (021) 220220 <br> Email: forestfireprediction@gmail.com</p>
        </div>
    </footer>
@endsection
