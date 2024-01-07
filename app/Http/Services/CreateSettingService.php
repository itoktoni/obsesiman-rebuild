<?php

namespace App\Http\Services;

use Plugins\Alert;
use GeoSot\EnvEditor\Facades\EnvEditor as EnvEditor;

class CreateSettingService
{
    public function save($data)
    {
        $check = false;
        try {

            EnvEditor::editKey('APP_NAME', setString($data->name));
            EnvEditor::editKey('APP_TITLE', setString($data->title));
            EnvEditor::editKey('APP_LOCATION', $data->location);
            EnvEditor::editKey('TRANSACTION_DAY_ALLOWED', $data->transaction_day);
            EnvEditor::editKey('TRANSACTION_ACTIVE_RS_ONLY', $data->transaction_active);
            EnvEditor::editKey('TRANSACTION_CHUNK', $data->transaction_chunk);

            EnvEditor::editKey('CODE_BERSIH', $data->code_bersih);
            EnvEditor::editKey('CODE_KOTOR', $data->code_kotor);
            EnvEditor::editKey('CODE_RETUR', $data->code_retur);
            EnvEditor::editKey('CODE_REWASH', $data->code_rewash);

            EnvEditor::editKey('TELESCOPE_ENABLED', $data->telescope_enable);
            if($data->telescope_enable){
                EnvEditor::editKey('APP_DEBUG', $data->telescope_enable);
            } else  {
                EnvEditor::editKey('APP_DEBUG', 0);
            }

            if ($data->has('logo')) {
                $file_logo = $data->file('logo');
                $extension = $file_logo->extension();
                $name = 'logo.' . $extension;
                // $name = time().'.'.$name;

                $file_logo->storeAs('/public/', $name);
                EnvEditor::editKey('APP_LOGO', $name);
            }

            Alert::update();

        } catch (\Throwable $th) {
            Alert::error($th->getMessage());
            return $th->getMessage();
        }

        return $check;
    }
}
