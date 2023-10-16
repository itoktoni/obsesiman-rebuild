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
            EnvFacades::setValue('APP_LOCATION', $data->location);
            EnvFacades::setValue('TRANSACTION_DAY_ALLOWED', $data->transaction_day);
            EnvFacades::setValue('TRANSACTION_ACTIVE_RS_ONLY', $data->transaction_active);
            EnvFacades::setValue('TRANSACTION_CHUNK', $data->transaction_chunk);

            EnvFacades::setValue('CODE_BERSIH', $data->code_bersih);
            EnvFacades::setValue('CODE_KOTOR', $data->code_kotor);
            EnvFacades::setValue('CODE_RETUR', $data->code_retur);
            EnvFacades::setValue('CODE_REWASH', $data->code_rewash);

            EnvFacades::setValue('TELESCOPE_ENABLED', $data->telescope_enable);

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
