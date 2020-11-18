<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\UploadedFile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        \Blade::if('role', function ($role) {
            $roles = ['dev', 'admin', 'moder', 'streamer', 'user'];
            if (auth()->user()) {
                $indexUser = array_keys($roles, auth()->user()->role);
                $indexPerm = array_keys($roles, $role);

                if($indexUser > $indexPerm)
                    return 0;
                else return 1;
            }
            return 0;
        });
        \Validator::replacer('max', function ($message, $attribute, $rule, $parameters) {
            // dd($message, $attribute, $rule, $parameters);
            return str_replace(':' . $rule, $parameters[0] / 1024, $message);
        });
    }
}
