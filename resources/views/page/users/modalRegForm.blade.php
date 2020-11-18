<div class="popup" id="modalRegForm">
    <div class="popup-inner">
        <div class="popup__photo d-md-flex d-none">
            <img src="{{ asset('assets/img/users/modalRegForm.png') }}">
        </div>
        <div class="popup__text p-3">
            <form method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label>Имя призывателя</label>
                        <div class="input-group">
                            <div class="input-group-prepend iconProfile">
                                @if($profileIconId)                                    
                                    <img style="height: calc(1.6em + .75rem + 2px);" 
                                    src="https://ddragon.leagueoflegends.com/cdn/10.10.3208608/img/profileicon/{{ $profileIconId }}.png">
                                @else
                                    <img style="height: calc(1.6em + .75rem + 2px);" 
                                    src="https://ddragon.leagueoflegends.com/cdn/10.10.3208608/img/profileicon/29.png">
                                @endif
                            </div>
                            {{ Form::text('summonername', request()->get('nickname') ? request()->get('nickname') : '', [
                                    'class'         => request()->get('nickname') ? 'form-control is-valid' : 'form-control',
                                    'disabled'      => request()->get('nickname') ? true : false,
                                    'placeholder'   => 'Кошечка Миу'
                            ]) }}
                            <div class="input-group-append">
                                {{ Form::button('Проверить', ['class' => 'btn btn-primary', 'disabled' => request()->get('nickname') ? true : false ]) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <ol class="pl-3">
                           
                                <li>Нажать кнопку "Проверить"</li>
                                <li>Установить предложенную иконку</li>
                                <li>Нажать кнопку "Проверить"</li>
                            
                                {{-- <li>Вставить проверочный код "<abbr id="verifyCode" class="sel-all initialism1">{{ request()->get('verifyCode') }}</abbr>"<br>в лаунчер League of Legends</li>
                                <li>Нажать кнопку "Проверить"</li> --}}
                            
                        </ol>
                    </div>
                </div>

                        
                <div class="form-row">
                    <div id="rang" class="form-group col-md-8">
                        <label>Лига</label>                                         
                        <div class="dropdown w-100">
                            <button type="button" class="btn btn-primary w-100 dropdown-toggle" 
                                data-toggle="dropdown" aria-expanded="true">{{ __('page.user.modal.select-mmr') }}</button>
                        
                            @if ($leagues)                              
                            {{ Form::hidden('rang') }}
                            <ul class="dropdown-menu w-100">
                                @foreach ($leagues as $slug => $league)                                   
                                    <li class="dropdown-item" data-rang="{{ $slug }}" {{ is_array($league['division']) ? "data-division" : "" }}>
                                        <span>{{ $league['name'] }}</span> {{ Html::image(asset('assets/img/LeagueOfLegends/leagues/'.$slug.'_1.png')) }}
                                    </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>                           
                    </div>
                    <div class="form-group col-md-4" style="display:none;">
                        <label>Дивизион</label>     
                        {{ Form::select('division', [
                            1 => 'I',
                            2 => 'II',
                            3 => 'III',
                            4 => 'IV',
                        ], 4, ['class' => 'custom-select']) }} 
                    </div>
                </div>

                <div class="form-row">
                    <div id="roles" class="col-md-12">
                        <label>Роли <small class="text-muted">Переместите роли от большего к меньшему</small></label>
                        <ul class="list-unstyled profile-roles-list-js">
                            <li data-role="top" >{{ Html::image(asset('assets/img/LeagueOfLegends/roles/top.png')) }}Топ</li>                           
                            <li data-role="jung">{{ Html::image(asset('assets/img/LeagueOfLegends/roles/jung.png'))}}Лес</li>
                            <li data-role="mid" >{{ Html::image(asset('assets/img/LeagueOfLegends/roles/mid.png')) }}Мид</li>
                            <li data-role="adc" >{{ Html::image(asset('assets/img/LeagueOfLegends/roles/adc.png')) }}Адк</li>
                            <li data-role="sup" >{{ Html::image(asset('assets/img/LeagueOfLegends/roles/sup.png')) }}Саппорт</li>
                        </ul>
                    </div>
                </div>

                <div class="form-row justify-content-center">
                    <button type="submit" class="btn btn-primary">Сохранить</button> 
                    <a class="btn btn-primary ml-3" href="{{ route('auth.logout') }}">{{ __('page.auth.logout') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>



@push('scripts')
<script>
    $(document).ready(function(){
        $("#verifyCode").tooltip({
            html: true,
            title:
            `<ol class="pl-3 text-left">
                <li>В игровом клиенте откройте настройки, выберите вкладку "Подтверждение" в левом меню, и вставьте туда скопированную строку.</li>
                <li>Сохраните изменения в игровом клиенте, подождите пару секунд и нажмите кнопку "Подтвердить".</li>
            </ol>`
        });

        var rolesSelector = '.profile-roles-list-js';
        new Sortable(document.querySelectorAll(rolesSelector), {
            draggable: 'li',
            mirror: {
                appendTo: rolesSelector,
                constrainDimensions: true,
            }
        });

        $("#rang li").on('click', function (e) {      
            $('[name=rang]').val($(this).data('rang'));
            btn = $(this).closest('.form-group').find('button');
            btn.html($(this).html())
            if($(this).data('division')!=undefined) 
                $('[name=division]').parent().show();
            else $('[name=division]').parent().hide();
        });

        $("[name=summonername] ~ .input-group-append button").on('click', function (e) {
            $name = $("[name=summonername]");
            if($name.val()!= ""){
                $(e.target).prop('disabled', true);
                axios.post('/profile', {
                    action: 'verifyAccount',
                    summonername: $name.val()
                }).then((res)=>{
                    if(res.data.status == 'success'){
                        if(res.data.messange == 'setIcon'){
                            $('.iconProfile').html(
                                '<img style="height: calc(1.6em + .75rem + 2px);" ' +
                                'src="http://ddragon.leagueoflegends.com/cdn/10.10.3208608/img/profileicon/' + res.data.code + '.png">')
                                $(e.target).removeAttr('disabled'); 
                        } 
                        if(res.data.messange == 'verified'){
                            $name.prop('disabled', true);
                            $name.addClass('is-valid');
                            $name.removeClass('is-invalid'); 
                        }                   
                    } else {
                        console.log();
                        toastr["error"](res.data.errors[0].messange)
                        $name.addClass('is-invalid');
                        $(e.target).removeAttr('disabled');                      
                    }
                });
            } else {
                $name.addClass('is-invalid');
            }

        });

        $("#modalRegForm form").submit(function (e) {
            e.preventDefault();
            $('button[type=submit]', this).prop('disabled', true);

            var rolesSortable = document.querySelectorAll(rolesSelector + ' li');
            var rolesOrdered = [];
            for (var i = 0; i < rolesSortable.length; i++) {
                rolesOrdered.push($(rolesSortable[i]).data('role'));
            }

            axios.post('/profile', {
                action: 'modalRegForm',
                roles: rolesOrdered,
                rang: $('[name=rang]').val(),
                division: $('[name=division]').val(),
            }).then((res)=>{
                if(res.data.status == 'success'){
                    $("#modalRegForm").removeClass('open');
                    $("body").removeClass('modal-open');
                    location.reload();
                } else {
                    $name.addClass('is-invalid');
                    $('button[type=submit]', this).removeAttr('disabled');
                }
            });
        })
        $("#modalRegForm").addClass('open');
        $("body").addClass('modal-open');
    });
</script>
@endpush
