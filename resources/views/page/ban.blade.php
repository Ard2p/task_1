@extends('layouts.public')


@section('header')
    <div class="fullscreen-video-wrap">
        <video id="videoBG" playsinline autoplay loop muted>
            <source src="{{ asset('assets/videos/Kayle_amp_Morgana_the_Righteous_amp_the_Fallen__Login_Screen_-_League_of_Legends.mp4') }}" type="video/mp4">
        </video>
        <div class="header-overlay">
            <div class="container header-content">
                <h1>Банан</h1>
                <p>
                    Я думаю вы понимаете причину по которой вы видете эту страницу,
                    <br>
                    каждое наше решение или поступок приводит к определенным последствиям!
                    <br><br>
                    Подумайте над своим поведением!
                </p>               
            </div>
        </div>
    </div>
@stop


@section('content')

@stop


@push('scripts')
<script>
    $(document).ready(function() {
        var videoBG = document.getElementById("videoBG");
        videoBG.volume = .05;
    });
</script>
@endpush
