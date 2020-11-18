<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @section('title')
        {{ __('page.site.name') }}
        @show
    </title>

    <link href="{{ mix('assets/app.css') }}" rel="stylesheet">

    @stack('links')

    
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark main-nav">
            <div class="container">
                {{-- <a class="navbar-brand" href="{{ route('index') }}">{{ Html::image(asset('assets/img/logo.png')) }}</a> --}}
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('index') }}">{{ __('page.index.name') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tour.index') }}">{{ __('page.tour.index.name') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('rating')     }}">{{ __('page.ratings.index.name') }}</a>
                        </li>
                        @role('moder')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profiles.index') }}">{{ __('page.user.profiles.name') }}</a>
                            </li>
                        @endrole
                        @role('streamer')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tour.create') }}">{{ __('page.tour.create.name') }}</a>
                            </li>
                        @endrole
                    </ul>
                    <ul class="navbar-nav ml-auto auth">
                        @guest
                            <li class="nav-item">
                                {{ Html::link(route('auth.social', ['social' => 'vkontakte']),
                                    __('page.auth.socials.vk'), ['class' => 'btn vk']) }}
                            </li>
                            <li class="nav-item">
                                {{ Html::link(route('auth.social', ['social' => 'twitch']),
                                    __('page.auth.socials.twitch'), ['class' => 'btn twitch']) }}
                            </li>
                        @endguest

                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile') }}">{{ __('page.user.profile.name') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('auth.logout') }}">{{ __('page.auth.logout') }}</a>
                            </li>                           
                        @endauth

                        {{-- <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">            Dropdown on Right</a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action with a lot of text inside of an item</a>
                            </div>
                        </li> --}}
                    </ul>
                </div>
            </div>
        </nav>

        @yield('header')

    </header>

    @yield('content')

    <footer>
        <div class="container">      
          
        </div>
    </footer>

    @includeWhen($modalRegForm ?? 0, 'page.users.modalRegForm')

    <script src="{{ mix('assets/app.js') }}"></script>

    @stack('scripts')

</body>
</html>
