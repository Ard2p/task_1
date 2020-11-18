<?php

Route::get('/',                                             'IndexController@index')            ->name('index');

// Авторизация
Route::get ('/login/{social}',                              'Auth\SocialController@redirect')   ->name('auth.social');
Route::get ('/login/{social}/callback',                     'Auth\SocialController@callback');

// Турниры
Route::get ('/tournaments',                                 'TournamentsController@index')      ->name('tour.index');
Route::post('/tournaments/{game}/callback',                 'TournamentsController@callback');

Route::get ('/ratings',                                     'RatingsController@index')          ->name('rating');

// Авториованные
Route::middleware(['auth'])->group(function(){

    Route::get('/logout',                                   'Auth\SocialController@logout')     ->name('auth.logout');

    // Профили
    Route::get('/profile',                                  'UsersController@profile')          ->name('profile');
    Route::get('/profiles',                                 'UsersController@profiles')         ->name('profiles.index');
    Route::get('/profiles/{id}',                            'UsersController@show')             ->name('profiles.show');

    Route::post('/profile',                                 'UsersController@update');

    // Турниры
    Route::get('/tournaments/create',                       'TournamentsController@create')     ->name('tour.create');
    Route::get('/tournaments/{game}',                       'TournamentsController@game')       ->name('tour.game');
    Route::get('/tournaments/{game}/{type}',                'TournamentsController@type')       ->name('tour.type');
    Route::get('/tournaments/{game}/{type}/{id}',           'TournamentsController@show')       ->name('tour.show');
    Route::get('/tournaments/{game}/{type}/{id}/edit',      'TournamentsController@edit')       ->name('tour.edit');

    Route::post('/tournaments',                             'TournamentsController@reg');
    Route::post('/tournaments/edit',                        'TournamentsController@update');
    Route::post('/tournaments/create',                      'TournamentsController@store');


    
    Route::get ('/discord',                                 'DiscordController@index')          ->name('discord');    
    Route::post('/discord',                                 'DiscordController@push');

    
    // // Анкета
    // Route::middleware(['Req'])->group(function(){});
});
