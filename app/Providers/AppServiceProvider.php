<?php

namespace App\Providers;

use App\Dao\Enums\RoleLevel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use msztorc\LaravelEnv\Env;
use Illuminate\Support\Facades\Blade;
use Plugins\Query;
use ProtoneMedia\LaravelFormComponents\Components\Form;
use ProtoneMedia\LaravelFormComponents\Components\FormInput;
use ProtoneMedia\LaravelFormComponents\Components\FormSelect;

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
        foreach($roles as $role){
            Blade::if($role->field_primary, function () use($role) {
                return auth()->check() && auth()->user()->role == $role->field_primary;
            });
        }

        Blade::if('level', function ($value) {
            return auth()->check() && auth()->user()->level >= $value;
        });
    }
}
