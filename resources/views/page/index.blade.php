@extends('layouts.public')


@section('header')
    <div class="fullscreen-video-wrap">
        <video id="videoBG" playsinline autoplay loop muted>
            <source src="{{ asset('assets/videos/Kayle_amp_Morgana_the_Righteous_amp_the_Fallen__Login_Screen_-_League_of_Legends.mp4') }}" type="video/mp4">
        </video>
        <div class="header-overlay">
            {{-- <div class="container header-content">
                <h1>GOOD MORNING</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit hic eaque rerum cupiditate officiis recusandae iusto quaerat architecto odio illum.</p>
                <a class="btn btn-success my-btn mt-4">KNOW MORE &gt;</a>
            </div> --}}
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
