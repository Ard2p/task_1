@extends('layouts.public')

@push('scripts')
    <script src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>
@endpush

@section('content')
<div class="container">

    <h1 class="my-4">{{ __('page.tour.create.name') }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="list-group">
                @foreach ($errors->all() as $error)
                    <li class="ml-2">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{ Form::open(['action' => ['TournamentsController@store'], 'files' => true]) }}
        <div class="form-row">

            <div class="form-group col-md-8">
                {{ Form::label('name', 'Название') }}
                {{ Form::text( 'name', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group col-md-4">
                {{ Form::label( 'status', 'Статус') }}
                {{ Form::select('status', [
                    'create' => 'Новый', 'pending' => 'Ожидание', 'open'  => 'Открыт',
                ], null, ['class' => 'custom-select']) }}
            </div>


            <div class="form-group col-md-4">
                {{ Form::label( 'game', 'Игра') }}
                {{ Form::select('game', [
                    'lol' => ' League of Legends'
                ], null, ['class' => 'custom-select']) }}
            </div>
            <div class="form-group col-md-4">
                {{ Form::label( 'type', 'Тип') }}
                {{ Form::select('type', [
                    'rtc'         => 'RTC',
                    'rtc_playoff' => 'RTC Плей-офф'
                ], null, ['class' => 'custom-select']) }}
            </div>
            {{-- <div class="form-group col-md-2">
                {{ Form::label( 'tourn_id', 'Турнир ID') }}
                {{ Form::select('tourn_id', [], null, ['class' => 'custom-select']) }}   
            </div>
            <div class="form-group col-md-2">
                {{ Form::label( 'tourn_id_label', 'Создать новый турнир ID') }}
                {{ Form::submit('Новый', ['class' => 'btn btn-primary col-md-12']) }}
            </div> --}}
            

            <div class="form-group col-md-6">
                {{ Form::label('img', 'Изображение на фон') }}
                <div class="custom-file">
                    {{ Form::file( 'img', ['class' => 'custom-file-input', 'disabled' => false]) }}
                    {{ Form::label('img', 'Выберите изображение...', ['class' => 'custom-file-label text-truncate']) }}
                </div>
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('twitch', 'Ссылка на Twitch') }}
                <div class="input-group">
                    <div class="input-group-prepend">
                        {{-- {{ Form::button('Twitch.tv/', ['class' => 'form-control']) }} --}}
                        <span class="input-group-text">Twitch.tv/</span>
                    </div>
                    {{ Form::text('twitch', null, ['class' => 'form-control', 'placeholder' => 'riotgames']) }}
                </div>
            </div>


            <div class="form-group col-md-12">
                {{ Form::label('desc', 'Описание') }}
                {{ Form::textarea('desc', null, ['class' => 'form-control']) }}
            </div>

            <div class="form-group col-md-12">
                {{ Form::submit('Создать', ['class' => 'btn btn-primary float-right col-md-2']) }}
            </div>

        </div>
    {{ Form::close() }}
</div>
@stop

@push('scripts')
<script>
    $(document).ready(function(){
        custom_file_label_text = $('.custom-file-label').text();
      
        tinymce.init({
            selector: 'textarea#desc',
            language: 'ru',
            menubar: true,
            statusbar: false,
            plugins: 'preview paste importcss searchreplace autolink save directionality code fullscreen image link media template codesample table charmap hr nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            // plugins: "link",
            // menubar: "insert",
            // toolbar: "link",
            default_link_target: "_blank"
        });
    });
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        if (fileName == '')
            fileName = custom_file_label_text;
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });
</script>
@endpush
