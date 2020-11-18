@extends('layouts.public')


@section('header')

@stop


@section('content')
<div class="container">

    <h1 class="my-4">{{ __('page.user.profile.name') }} ID: <?=Auth::user()->id;?></h1>

        @php
            $expAll = Auth::user()->exp;
            $expNow = $expAll % 1000;
            $lvl = (int)($expAll / 1000) + 1;
            $percentNow = $expNow / 1000 * 100;        
        @endphp
     
        <div class="row">              
            <div class="col-md-4 mb-5">                
                <h3>Уровень: <?=$lvl?></h3>
                <div><?=$expNow?> / 1000</div>
                <div class="progress" style="align-text:center;">
                    <div class="progress-bar progress-bar-striped bg-success" role="progressbar" 
                        style="width:<?=$percentNow?>%;" 
                        aria-valuenow="<?=$expNow?>" aria-valuemin="0" aria-valuemax="1000"></div>
                    
                </div>         
            </div>
        </div> 


        @if($socials)       
        <div class="row">              
            <div class="col-md-4 mb-5"> 
                <h2>Социальные сети <br>(Отвязка не работает =P)</h2>
                <div class="row"> 
                    <div class="col-md-6">Вконтакте</div>    
                      @if (isset($socials['vkontakte']))             
                        {{ Form::button('Отвязать', [
                            'class'  => 'btn btn-primary col-md-4 social',
                            'social' => 'vkontakte',
                            'disabled' => true
                        ]) }}
                      @else 
                        <a href="/login/vkontakte" class='btn btn-primary col-md-4 social'>Привязать</a>
                      @endif
                </div> 
                <div class="row mt-2"> 
                    <div class="col-md-6">Twitch</div>  
                      @if (isset($socials['twitch']))             
                      {{ Form::button('Отвязать', [
                          'class'  => 'btn btn-primary col-md-4 social',
                          'social' => 'twitch',
                          'disabled' => true
                      ]) }}
                      @else 
                        <a href="/login/twitch" class='btn btn-primary col-md-4 social'>Привязать</a>
                      @endif
                </div> 
            </div>
        </div> 
        @endif

        <div class="row">  
            <div class="col-md-6 mb-5"> 
                <h2>Игровые профили</h2>
                <div class="row"> 
                    <div class="col-md-4">Игра</div> 
                    <div class="col-md-5">Никнейм</div>
                    <div class="col-md-3">Смена ника</div>
                </div> 

                @if($accounts)
                    @foreach ($accounts as $item)
                    <div class="row">
                        <div class="col-md-4">{{ __('page.games.'.$item->game) }}</div> 
                        <div class="col-md-5 nick">{{ $item->nickname }}</div>
                        {{ Form::button('Обновить', [
                            'class'     => 'refreshAcc btn btn-primary col-md-3',
                            'data-nick' => $item->nickname
                        ]) }}
                    </div> 
                    @endforeach
                @endif
               
            </div>
        </div>        
        <div class="row justify-content-center1">            
            <form class="col-md-6" id="rolesEdit" method="POST">
                {{-- @csrf --}}

                <div class="form-row">
                    <div id="roles" class="col-md-12">
                        <label>Роли <small class="text-muted">Переместите роли от большего к меньшему</small></label>
                        <ul class="list-unstyled profile-roles-list-js">
                            @php
                                $roles = json_decode(Auth::user()->roles);
                            @endphp
                            @foreach ($roles as $item)
                                <li data-role="{{ $item }}" >
                                    {{ Html::image(asset('assets/img/LeagueOfLegends/roles/'.$item.'.png')) }}
                                    {{ __('riot.lol.roles.'.$item) }}
                                </li>  
                            @endforeach                         
                        </ul>
                    </div>
                </div>

                <div class="form-row justify-content-center">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>

        </div>  
    </div>  
@stop


@push('scripts')
<script>
    $(document).ready(function(){
        var rolesSelector = '.profile-roles-list-js';
        new Sortable(document.querySelectorAll(rolesSelector), {
            draggable: 'li',
            mirror: {
                appendTo: rolesSelector,
                constrainDimensions: true,
            }
        });

        $(".refreshAcc").on('click', function (e) {
            btn = $(e.target)     
            btn.prop('disabled', true);
            axios.post('/profile', {
                action: 'refreshAcc',
                nickname: btn.data('nick')               
            }).then((res)=>{                  
                if(res.data.status == 'success'){                  
                    btn.closest('.row').find('.nick').text(res.data.nick);
                } else {                  
                    btn.removeAttr('disabled');
                }
            });
        })

        $("form#rolesEdit").submit(function (e) {
            e.preventDefault();
            $('button[type=submit]', this).prop('disabled', true);

            var rolesSortable = document.querySelectorAll(rolesSelector + ' li');
            var rolesOrdered = [];
            for (var i = 0; i < rolesSortable.length; i++) {
                rolesOrdered.push($(rolesSortable[i]).data('role'));
            }

            axios.post('/profile', {
                action: 'rolesEdit',
                roles: rolesOrdered               
            }).then((res)=>{                  
                if(res.data.status == 'success'){
                    $('button[type=submit]', this).removeAttr('disabled');
                } else {                  
                    $('button[type=submit]', this).removeAttr('disabled');
                }
            });
        })

    });
</script>
@endpush