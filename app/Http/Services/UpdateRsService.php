<?php

namespace App\Http\Services;

use App\Dao\Interfaces\CrudInterface;
use Plugins\Alert;

class UpdateRsService
{
    public function update(CrudInterface $repository, $data, $code)
    {
        $check = $repository->updateRepository($data, $code);
        if ($check['status']) {
            $check['data']->has_ruangan()->sync($data['ruangan']);
            if(request()->wantsJson()){
                return response()->json($check)->getData();
            }
            Alert::update();
        } else {
            Alert::error($check['data']);
        }
        return $check;
    }
}
