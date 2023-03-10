<?php

namespace App\Http\Services;

use App\Dao\Facades\EnvFacades;
use Plugins\Alert;

class CreateSettingService
{
    public function save($data)
    {
        $check = false;
        try {

            EnvFacades::setValue('APP_NAME', $data->name);
            EnvFacades::setValue('APP_TITLE', $data->title);

            if ($data->has('logo')) {
                $file_logo = $data->file('logo');
                $extension = $file_logo->extension();
                $name = 'logo.' . $extension;
                // $name = time().'.'.$name;

                $file_logo->storeAs('/public/', $name);
                EnvFacades::setValue('APP_LOGO', $name);
            }

            Alert::update();

        } catch (\Throwable $th) {
            Alert::error($th->getMessage());
            return $th->getMessage();
        }

        return $check;
    }
}
