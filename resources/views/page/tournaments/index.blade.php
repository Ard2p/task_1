@extends('layouts.public')

@section('content')
<div class="container">

    <h1 class="my-4">{{ __('page.tour.index.name') }}</h1>

    @if ($tournaments)
        <section id="tournaments" class="form-row">

            @foreach ($tournaments as $tour)    
                @auth @php
                    $user_reg = $regUserTours->has($tour->id);          
                    $btn_name = $user_reg ? 'exit': 'enter';    

                    $is_enter = in_array($tour->status, ['open', 'balance']);
                    $is_exit  = in_array($tour->status, ['open']);

                    $is_btn   = (!$user_reg && $is_enter) || ($user_reg && $is_exit);                                  
                @endphp @endauth
                <div class="col-md-4 mb-3">
                    <div class="card border-0">

                        <img class="card-img-top" src="{{ $tour->img ? asset('storage/'.$tour->img) : asset('assets/img/LeagueOfLegends/tournaments/rtc.png') }}">
                        <div class="card-body border p-3">

                            <h5 class="card-title">{{ $tour->name }}</h5>
                            <p class="card-text">  
                                
                                @role('moder')                             
                                <div class="dropdown">
                                    <div class="dropdown-toggle" 
                                        data-toggle="dropdown" aria-expanded="true">{{ __('page.tour.status.'.$tour->status) }}</div>
                                                   
                                    <ul class="dropdown-menu" data-action="tour_status" data-id="{{ $tour->id }}">
                                        <li class="dropdown-item btn-status-tour-js" data-status="create"   >@lang('page.tour.status.create'    )</li>       
                                        <li class="dropdown-item btn-status-tour-js" data-status="pending"  >@lang('page.tour.status.pending'   )</li>    
                                        <li class="dropdown-item btn-status-tour-js" data-status="open"     >@lang('page.tour.status.open'      )</li>   
                                        <li class="dropdown-item btn-status-tour-js" data-status="balance"  >@lang('page.tour.status.balance'   )</li>   
                                        <li class="dropdown-item btn-status-tour-js" data-status="process"  >@lang('page.tour.status.process'   )</li>   
                                        <li class="dropdown-item btn-status-tour-js" data-status="end"      >@lang('page.tour.status.end'       )</li>   
                                        <li class="dropdown-item btn-status-tour-js" data-status="arhive"   >@lang('page.tour.status.arhive'    )</li>                               
                                    </ul>                                   
                                </div>   
                                @else
                                    @lang('page.tour.status.'.$tour->status)
                                @endrole     
                            </p>                            
                            @auth
                            <div class="col-12 p-0">
                                <div class="input-group justify-content-center">
                                    <div class="w-50 input-group-prepend">
                                        {{ Html::link(route('tour.show', [
                                            'game' => $tour->game, 'type' => $tour->type, 'id' => $tour->id
                                        ]), 'Подробнее', [
                                            'class' => 'w-100 btn btn-primary'
                                        ]) }}
                                    </div>
                                    <div class="w-50 input-group-append">
                                        {{ Form::button(__('page.tour.btn-reg.'.$btn_name), [
                                            'class' => [
                                                'w-100 btn btn-reg',
                                                $user_reg ? ' btn-danger' : ' btn-success'
                                            ],
                                            'disabled'      =>!$is_btn,
                                            'data-action'   => $user_reg ? 'tour_exit' : 'tour_enter',
                                            'data-id'       => $tour->id
                                        ]) }}
                                    </div>
                                </div>
                            </div>
                            @endauth

                        </div>
                    </div>
                </div>
            @endforeach

        </section>
    @endif


</div>
@stop


@push('scripts')
<script>
    $(document).ready(function(){
        $(document).on('click', "#tournaments .btn-reg", function (e) {
            console.log('ready');
            $(e.target).prop('disabled', true);
            axios.post('/tournaments', {
                action: $(e.target).data('action'),
                id:     $(e.target).data('id')
            }).then((res)=>{
                console.log(res.data);
                if(res.data.status == 'success'){                    
                    $(e.target).removeAttr('disabled');
                    // $(e.target).prop('disabled', true);
                    $(e.target).text(res.data.btn_name);
                    if (res.data.code == 'enter'){
                        $(e.target).addClass('btn-success');
                        $(e.target).removeClass('btn-danger');
                        $(e.target).data('action', 'tour_enter');
                    } else {
                        $(e.target).addClass('btn-danger');
                        $(e.target).removeClass('btn-success');
                        $(e.target).data('action', 'tour_exit');
                    }
                } else {     
                    console.log(res.data)               
                    toastr["error"](res.data.messange)
                    $(e.target).removeAttr('disabled');
                }
            });
        });

        $(document).on('click', "#tournaments .btn-status-tour-js", function (e) {
            console.log('click');
            $(e.target).prop('disabled', true);
            console.log($(e.target).parent().data('id'));
            console.log($(e.target).parent());
            console.log($(e.target));
            axios.post('/tournaments/edit', {
                action: $(e.target).parent().data('action'),
                status: $(e.target).data('status'),
                id:     $(e.target).parent().data('id')
            }).then((res)=>{
                if(res.data.status == 'success'){              
                    location.reload();      
                    $(e.target).parent().removeAttr('disabled');    
                    $(e.target).closest('.dropdown').find('.dropdown-toggle').text($(e.target).text());                   
                } else {            
                    toastr["error"](res.data.status)
                    $(e.target).parent().removeAttr('disabled');
                }
            });
        });

    });
</script>
@endpush
