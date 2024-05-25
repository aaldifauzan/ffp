@extends('layouts.main')

@section('container')
    <div class="home-image-container">
        <div class="home-dark-overlay"></div>
        <img src="{{ asset('img/home1.png') }}" alt="Forest Fire" class="home-image">
        <div class="home-overlay">
            <h1 class="home-title">Welcome to Forest Fire Prediction</h1>
            <p class="home-text">Empowering communities and protecting nature through advanced fire prediction technology. Join us in safeguarding our forests and wildlife.</p>            
        </div>
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
            <p><strong>Contact Us:</strong> <br> Forest Fire Prediction <br> Telp: (021) 220220 <br> Email: forestfirepredictionelm@gmail.com</p>
        </div>
    </footer>
@endsection
