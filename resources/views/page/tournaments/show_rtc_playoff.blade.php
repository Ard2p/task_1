@extends('layouts.public')

@section('content')
<div class="container">

    <h1 class="my-4">{{ $tournament->name }}</h1>

    <section id="tournament">

        <div class="form-row mb-5">
            <div class="col-md-6 sel">{!! $tournament->desc !!}</div>
            <div class="col-md-6">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe id="twitch" class="embed-responsive-item" src="https://player.twitch.tv/?channel={{ $tournament->twitch }}&muted=true&parent=ff15.ru"
                        allowfullscreen="true" >
                    </iframe>
                </div>
            </div>
        </div>


        <div class="form-row mb-5">          
            <div id="teams" class="col-md-12 sel">      
              
              {{-- <h2>Турнирная сетка РТК Плей-офф</h2>

              <h2>Временно не отображаеться!</h2> --}}

            
            	@if($tournament->teams)                  
                   
                <h2>Турнирная сетка РТК Плей-офф</h2>
                

                @php
                  $teams_current = $tournament->teams[array_key_last($tournament->teams)]; 
                  if($team)
                    $team_index = array_search($team, array_column($teams_current, 'team'));
                  else  $team_index = false;
                @endphp
      
                @role('streamer')
                  @for ($i = 0; $i < count($teams_current); $i++)  
                    @if ($teams_current[$i]['code'])
                      {{ $teams_current[$i]['team'] }} => {{ $teams_current[$i]['code'] }}<br>
                    @endif
                  @endfor 
                @endrole   

                
        
                @if($team_index && $teams_current[$team_index]['code'])
                  <div> Ваш код для входа в турнир: {{ $teams_current[$team_index]['code'] }}</div>
                @endif



								@php 
									$tournament->teams = array_reverse($tournament->teams);
								@endphp

							@for ($t = 0; $t < count($tournament->teams); $t++)    
								@php 
									$count = count($tournament->teams[$t]);
									if($count == 2) 
										$stage = 'Финал';
									if($count == 4)
										$stage = 'Стадия 1/' . ceil($count / 4) * 2;
                  if($count >= 8)
                    $stage = 'Стадия 1/' . ceil($count / 8) * 4;
                  if($count >= 16)
                    $stage = 'Стадия 1/' . ceil($count / 16) * 8;
                  if($count >= 32)
                    $stage = 'Стадия 1/' . ceil($count / 32) * 16;

								@endphp
								<h3 class="mt-5">{{ $stage }}</h3>

								<div class="form-row mt-5">
									
										@for ($i = 0; $i < count($tournament->teams[$t]); $i=$i+2)    
											@php
												$team_1 = isset($tournament->teams[$t][$i  ]) ? $tournament->teams[$t][$i  ]['team'] : null;
												$team_2 = isset($tournament->teams[$t][$i+1]) ? $tournament->teams[$t][$i+1]['team'] : null;

												$win_1 = '';
												$win_2 = '';

												if(isset($tournament->teams[$t][$i  ]['win']))  
													$win_1 = 'style=color:#de69c4';   
												if(isset($tournament->teams[$t][$i+1]['win']))
													$win_2 = 'style=color:#de69c4'; 
																						
											@endphp    
											<div class="col-md-6 lobby">
												<h5 class="text-center">Игра #{{ intdiv($i, 2) + 1 }}</h5>
												<input type="hidden" value="{{ $tournament->teams[$t][$i]['mmr'] }}">
												<input type="hidden" value="{{ $team_2 ? $tournament->teams[$t][$i+1]['mmr'] : '' }}">
												<div class="form-row">                                                                
													<ul class="col-6 pr-0 team_sort">
														<h5 class="text-center" {{ $win_1 }}>Команда #{{ $team_1 }}</h5>  
										                         
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/top.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_1]['top' ]->nickname) ? $teams[$team_1]['top' ]->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/jung.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_1]['jung']->nickname) ? $teams[$team_1]['jung']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/mid.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_1]['mid']->nickname) ? $teams[$team_1]['mid']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/adc.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_1]['adc']->nickname) ? $teams[$team_1]['adc']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/sup.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_1]['sup']->nickname) ? $teams[$team_1]['sup']->nickname : '' }}</div>
													</li>
												</ul>
												<ul class="col-6 pl-0 team_sort">
													<h5 class="text-center" {{$win_2}}>Команда #{{ $team_2 ? $team_2 : '?' }}</h5>                                    
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/top.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_2]['top']->nickname) ? $teams[$team_2]['top']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/jung.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_2]['jung']->nickname) ? $teams[$team_2]['jung']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/mid.png') }}">
														<div class="player text-truncate">{{ isset($teams[$team_2]['mid']->nickname) ? $teams[$team_2]['mid']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/adc.png') }}">
														<div class="player  text-truncate">{{ isset($teams[$team_2]['adc']->nickname) ? $teams[$team_2]['adc']->nickname : '' }}</div>
													</li>
													<li class="slot">
														<img src="{{ asset('assets/img/LeagueOfLegends/roles/sup.png') }}">
														<div class="player  text-truncate">{{ isset($teams[$team_2]['sup']->nickname) ? $teams[$team_2]['sup']->nickname : '' }}</div>
													</li>
												</ul>
											</div>
										</div>
										@endfor  
								
								</div>
								@endfor 
                @endif

                <div class="form-row mt-5">

                    <div class="col-md-8 sel">
                        <h2>Запас</h2>
                        <ul class="team_sort zone mt-4 list-unstyled">
                            <div class="form-row1">
                                @php
                                  $playerTen = 1;
                                @endphp

                                @foreach ($teams[0] as $player)
                                    <li class="slot col-md-5th @if($playerTen % 10 == 0) mb-3 @endif">                                
                                        <div class="player text-truncate">{{ $player->nickname }}</div>                                
                                    </li>                                  
                                  @php
                                    $playerTen++;
                                  @endphp
                                @endforeach
                            </div>
                        </ul>
                        {{-- <div>Будте людми! Приходите на РТК!</div>
                        <img src="https://media.discordapp.net/attachments/437266172896083989/695643061899755608/unknown.png"> --}}
                    </div>

                    {{-- <div class="col-md-4 sel">
                        <h2>Штрафники</h2>
                        <ul class="team_sort zone mt-4">
                            @foreach ($teams[-1] as $player)
                            <li class="slot">                              
                                <div class="player">{{ $player->nickname }}</div>
                            </li>
                            @endforeach
                        </ul>
                    </div> --}}

                </div>
            </div>
        </div>


    </section>

</div>
@stop


@push('scripts')
<script>
    var droppableParent;

    $(document).ready(function(){


        // const swappable = new Swappable(document.querySelectorAll('ul.team_sort li.slot'), {
        //     draggable: '.player',
        //     mirror: {
        //         // constrainDimensions: true,
        //     },
        //     plugins: [Plugins.Snappable]
        // });
        // swappable.on('swappable:swap', (e) => {
        //     // var parent = $(e.overContainer).parent();
        //     // console.log(parent);
        //     // if(parent.hasClass('zone')){
        //     //     console.log('zone');
        //     //     e.cancel()
        //     //     $(e.over).appendTo(e.overContainer);
        //     // }
        // });
        // swappable.on('drag:stop', (e) => console.log(e));

        // var swappedContainer;
        // const draggable = new Draggable(
        //     document.querySelectorAll('ul.team_sort li.slot'), {
        //     draggable: '.player',
        //     mirror: {
        //         // constrainDimensions: true,
        //     },
        //     // plugins: [Plugins.Snappable]
        // });
        // draggable.on('drag:stop', (e) => {
        //     if(swappedContainer){
        //         $('.player', swappedContainer).appendTo(e.sourceContainer);
        //         $(e.source).appendTo(swappedContainer);
        //     }
        // });
        // draggable.on('drag:over:container', (e) => {
        //     // $(e.source).appendTo(e.overContainer);
        //     swappedContainer = e.overContainer;
        // });
        // draggable.on('drag:out:container', (e) => {
        //     swappedContainer = null;
        // });
        // draggable.on('drag:over', (e) => {
        //     // console.dir(e)
        //     // $(e.source).appendTo(e.overContainer);
        //     // swappedContainer = e.overContainer;
        // });
        // draggable.on('drag:out', (e) => {
        //     console.dir(e)
        // });


        // // swap(event.source, event.over);
        // function swap(source, over) {
        //     const overParent = over.parentNode;
        //     const sourceParent = source.parentNode;

        //     withTempElement(tmpElement => {
        //         sourceParent.insertBefore(tmpElement, source);
        //         overParent.insertBefore(source, over);
        //         sourceParent.insertBefore(over, tmpElement);
        //     });
        // }

        // function withTempElement(callback) {
        //     const tmpElement = document.createElement('div');
        //     callback(tmpElement);
        //     tmpElement.parentNode.removeChild(tmpElement);
        // }


        // $('ul.team_sort li.slot .player').draggable({
        //     revert: 'invalid',
        //     revertDuration: 200,
        //     start: function () {
        //         droppableParent = $(this).parent();
        //         $(this).addClass('dragged');
        //     },
        //     stop: function () {
        //         $(this).removeClass('dragged');
        //     }
        // });

        // $('ul.team_sort li.slot').droppable({
        //     hoverClass: 'drop-hover',
        //     drop: function (event, ui) {
        //         console.log('slot 1');
        //         var draggable = $(ui.draggable[0]),
        //             draggableOffset = draggable.offset(),
        //             container = $(event.target),
        //             containerOffset = container.offset();

        //         // console.dir($('.player', event.target));
        //         // console.dir($('.player', event.target).length != 0);

        //         // if($('.player', event.target).length != 0)
        //             $('.player', event.target).appendTo(droppableParent);
        //         // else droppableParent.remove();

        //             // .css({opacity: 0});
        //             // .animate({opacity: 1}, 200);

        //         draggable.appendTo(container).css({left: 0, top: 0});
        //         // .css({
        //         //     left: draggableOffset.left - containerOffset.left,
        //         //     top: draggableOffset.top - containerOffset.top
        //         // })
        //         // .animate({left: 0, top: 0}, 200);
        //     }
        // });

        // $('ul.team_sort.zone').droppable({
        //     hoverClass: 'drop-hover',
        //     drop: function (event, ui) {
        //         console.log('slot 2');
        //         var draggable = $(ui.draggable[0]),
        //             container = $(event.target);

        //         // console.dir($(event.target).parent().hasClass('zone'));
        //         // console.dir(container.parent().hasClass('zone'));

        //         // $('.player', event.target).appendTo(droppableParent);
        //         $("<li/>").addClass('slot').html(draggable.css({left: 0, top: 0})).appendTo(container);
        //     }
        // });

        // $("#tournaments .btn-js").on('click', function (e) {
        //     $(e.target).prop('disabled', true);
        //     axios.post('/tournaments', {
        //         action: 'tour_reg',
        //         id: $(e.target).data('id')
        //     }).then((res)=>{
        //         console.log(res);
        //         if(res.data.status == 'success'){
        //             $(e.target).prop('disabled', true);
        //         } else {
        //             $(e.target).removeAttr('disabled');
        //         }
        //     });
        // });
    });
</script>
@endpush
