<?php

namespace App\Http\Controllers;

use App\Dao\Enums\BooleanType;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRequest;
use App\Http\Services\DeleteService;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Plugins\Response;
use Plugins\Template;

class MinimalController extends Controller
{
    public static $service;
    public static $repository;
    public static $template;
    public static $share = [];

    protected function beforeForm(){}
    protected function beforeCreate(){}
    protected function beforeUpdate($code){}

    protected function share($data = [])
    {
        $status = BooleanType::getOptions();
        $view = [
            'status' => $status,
            'model' => false,
        ];
        return self::$share = array_merge($view, self::$share, $data);
    }

    public function getCreate()
    {
        $this->beforeForm();
        $this->beforeCreate();
        return moduleView(modulePathForm(), $this->share());
    }
}
