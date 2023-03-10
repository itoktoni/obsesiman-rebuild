<?php

use Coderello\SharedData\Facades\SharedData;
use Illuminate\Support\Str;

define('ACTION_CREATE', 'getCreate');
define('ACTION_UPDATE', 'getUpdate');
define('ACTION_DELETE', 'getDelete');
define('ACTION_EMPTY', 'empty');
define('ACTION_TABLE', 'getTable');
define('ACTION_PRINT', 'getPrint');
define('ERROR_PERMISION', 'Maaf anda tidak punya otorisasi untuk melakukan hal ini');

function module($module = null){
    return SharedData::get($module);
}

function moduleCode($name = null)
{
    return !empty($name) ? $name : SharedData::get('module_code');
}

function moduleName($name = null)
{
    return !empty($name) ? __($name) : __(SharedData::get('menu_name'));
}

function moduleAction($name = null)
{
    return moduleCode() . '.' . $name;
}

function moduleRoute($action, $param = false)
{
    return $param ? route(moduleAction($action), $param) : route(moduleAction($action));
}

function modulePath($name = null)
{
    return !empty($name) ? $name : moduleCode($name);
}

function modulePathTable($name = null)
{
    if ($name) {
        return 'pages.' . $name . '.table';
    }

    return 'pages.' . moduleCode() . '.table';
}

function modulePathPrint($name = null)
{
    if ($name) {
        return 'pages.' . $name . '.print';
    }

    return 'pages.' . moduleCode() . '.print';
}

function modulePathForm($name = null, $template = null)
{
    if ($template && $name) {
        return 'pages.' . $template . '.' . $name;
    }

    if ($name) {
        return 'pages.' . moduleCode() . '.' . $name;
    }

    if ($template) {
        return 'pages.' . $template . '.form';
    }

    return 'pages.' . moduleCode() . '.form';
}

function moduleView($template, $data){
    $view = view($template)->with($data);
    if(request()->header('hx-request') && env('APP_SPA', false)){
        $view = $view->fragment('content');
    }

    return $view;
}

function formatLabel($value){

    $label = Str::of($value);
    if($label->contains('_')){
        $label = $label = $label->explode('_')->last();
    }
    else{
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function formatAttribute($value){

    $label = Str::of($value);
    if($label->contains(' ')){
        $label = $label = $label->explode(' ')->last();
    }
    else{
        $label = $label->replace('[]', '');
    }

    return ucfirst($label);
}

function showValue($value){
    if($value == 0){
        return '';
    }

    return $value;
}
