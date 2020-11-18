@extends('layouts.public')


@section('header')

@stop


@section('content')
<div class="container">

    <h1 class="my-4">{{ __('page.user.profiles.name') }}</h1>

        <div class="row">  
            <div class="col-md-12 mb-5 sel"> 
                <h2>Игровые профили</h2>
                <div class="row">        
                    <div class="col-md-1">№</div>   
                    <div class="col-md-3">Никнейм</div>
                    <div class="col-md-1">ИД</div>  
                    <div class="col-md-1">Статус</div>
                    <div class="col-md-1">MMR</div>
                    <div class="col-md-3">Роли</div>
                </div> 

                @if($accounts)
                @php
                    $num = 0;
                @endphp
                    @foreach ($accounts as $item)
                    @php
                        $num++;
                        // $roles = join(" ", json_decode($item->user->roles)); 
                        $status = $item->user->status == 'ban' ? 1 : 0;                     
                    @endphp
                    <div class="row" @if($status) style="color:red" @endif>   
                        <div class="col-md-1">{{ $num }}</div>     
                        <div class="col-md-3">{{ $item->nickname }}</div>
                        <div class="col-md-1">{{ $item->user->id }}</div>        
                        <div class="col-md-1">{{ $item->user->status }}</div>
                        <div class="col-md-1">{{ $item->user->mmr }}</div>
                        {{-- <div class="col-md-4">{{ $roles }}</div> --}}
                        <div class="col-md-3">{{ $item->user->roles }}</div>
                    </div> 
                    @endforeach
                @endif
               
            </div>
        </div>  

    </div>  
@stop


@push('scripts')

@endpush