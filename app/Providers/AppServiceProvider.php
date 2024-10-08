<?php

namespace App\Providers;

use App\PersonalAccessToken;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Plugins\Query;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('env_facades', function () {
            return new Env();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        $roles = Query::role();
        if($roles){
            foreach($roles as $role){
                Blade::if($role->field_primary, function () use($role) {
                    return auth()->check() && auth()->user()->role == $role->field_primary;
                });
            }
        }

        Blade::if('level', function ($value) {
            return auth()->check() && auth()->user()->level >= $value;
        });

        Sanctum::usePersonalAccessTokenModel(
            PersonalAccessToken::class
        );
    }
}
