@extends('layouts.public')


@section('header')

@stop


@section('content')
<div class="container">

    <h1 class="my-4">{{ __('page.ratings.index.name') }}</h1>

        <div class="row">  
            <div class="col-md-12 mb-5 sel"> 
                <h2>Данные за @php echo date('m.y H:i', strtotime('+3 hour')); @endphp</h2>
                <div class="row">        
                    <div class="col-md-1">№</div>   
                    <div class="col-md-4">Никнейм</div>
                    <div class="col-md-1">Уровень</div>
                    <div class="col-md-1">Игр</div>  
                    <div class="col-md-1">Побед</div>
                    {{-- <div class="col-md-1">Стрик</div> --}}
                    <div class="col-md-3">У С С</div>
                </div> 

                @if($statistics)
                @php
                    $num = 0;
                @endphp
                    @foreach ($statistics as $items)
                    @foreach ($items as $item)
                    @php
                        $num++;    
                        $gameCount = $item->win + $item->lose;
                       
                        $K   = $item->k ? number_format($item->k / $gameCount, 1) : 0;
                        $D   = $item->d ? number_format($item->d / $gameCount, 1) : 0;
                        $A   = $item->a ? number_format($item->a / $gameCount, 1) : 0;
                        $KA  = $K + $A;

                        if ($KA == 0 ) $KDA = '0';
                        else if ($D == 0  ) $KDA = 'Перфект!!!';                         
                        else $KDA = number_format($KA / $D, 1);   

                        $level = (int)($item->user->exp / 1000) + 1;  

                        $streak = $item->account->streak;
                        $streakColor = $streak > 0 ? 'yellowgreen' : 'red';

                        $link = $num % 2 == 1 ? 'style=background:#1b1b1b': '';
                    @endphp
                    <div class="row" {{ $link }}>   
                        <div class="col-md-1">{{ $num }}</div>     
                        <div class="col-md-4">{{ $item->account->nickname }}</div>
                        <div class="col-md-1">{{ $level }}</div>        
                        <div class="col-md-1">{{ $gameCount }}</div>        
                        <div class="col-md-1">{{ $item->win }}</div>

                        {{-- <div class="col-md-1" 
                    style="color:{{ $streakColor }}">{{ abs($streak) }}</div> --}}

                        <div class="col-md-3">
                            <div class="form-row text-center">
                            <span class="col-md-3" style="color:yellowgreen" >{{ $item->k }}</span>
                            <span class="col-md-3" style="color:red"         >{{ $item->d }}</span> 
                            <span class="col-md-3" style="color:yellow"      >{{ $item->a }}</span>  
                            <span class="col-md-3" >{{ $KDA }}</span>
                            </div>
                        </div>  
                    </div> 
                    @endforeach
                    @endforeach
                @endif
               
            </div>
        </div>  

    </div>  
@stop


@push('scripts')

@endpush