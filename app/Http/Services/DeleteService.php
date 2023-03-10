<?php

namespace App\Http\Services;

use Illuminate\Validation\Rule;
use App\Dao\Interfaces\CrudInterface;
use Plugins\Alert;

class DeleteService
{
    public function delete(CrudInterface $repository, $code)
    {
        $rules = ['code' => 'required'];
        request()->validate($rules, ['code.required' => 'Please select any data !']);

        $check = $repository->deleteRepository($code);

        if ($check['status']) {
            Alert::delete();
        } else {
            Alert::error($check['data']);
        }

        if (request()->wantsJson()) {

            return response()->json($check)->getData();
        }

        return $check;
    }
}
