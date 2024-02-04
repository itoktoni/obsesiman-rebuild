<?php

namespace App\Http\Controllers;

use App\Dao\Models\Roles;
use App\Dao\Models\Supplier;
use App\Dao\Models\SystemRole;
use App\Dao\Models\User;
use App\Dao\Repositories\UserRepository;
use App\Http\Controllers\MasterController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Services\CreateService;
use App\Http\Services\SingleService;
use App\Http\Services\UpdateService;
use Illuminate\Support\Facades\Hash;
use Plugins\Notes;
use Plugins\Response;

class UserController extends MasterController
{
    public function __construct(UserRepository $repository, SingleService $service)
    {
        self::$repository = self::$repository ?? $repository;
        self::$service = self::$service ?? $service;
    }

    protected function beforeForm()
    {
        $roles = SystemRole::getOptions();

        self::$share = [
            'roles' => $roles,
        ];
    }

    public function postCreate(UserRequest $request, CreateService $service)
    {
        $data = $service->save(self::$repository, $request);
        return Response::redirectBack($data);
    }

    public function postUpdate($code, UserRequest $request, UpdateService $service)
    {
        $data = $service->update(self::$repository, $request, $code);
        return Response::redirectBack($data);
    }

    public function changePassword(){

        if(request()->method() == 'POST'){

            User::find(auth()->user()->id)->update([
                'password' => bcrypt(request()->get('password'))
            ]);

            return redirect()->route('home');
        }
        return view('auth.change_password')->with($this->share());
    }

    public function postLoginApi(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!Hash::check($request->password, $user->password)) {
            return Notes::error([
                'password' => 'Password Tidak Di temukan',
            ],'Login Gagal');
        }

        // if($user->tokens()){
        //     $user->tokens()->delete();
        // }

        $token = $user->createToken($user->name);
        $string_token = $token->plainTextToken;
        $user->api_token = $string_token;
        $user->save();

        return Notes::token($user->toArray());
    }
}
