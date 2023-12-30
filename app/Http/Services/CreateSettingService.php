<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Artisan;
use Plugins\Alert;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

class CreateSettingService
{
    public function save($data)
    {
        $check = false;
        try {

            DotenvEditor::setKey('APP_NAME', $data->name);
            DotenvEditor::setKey('APP_TITLE', $data->title);
            DotenvEditor::setKey('APP_LOCATION', $data->location);
            DotenvEditor::setKey('TRANSACTION_DAY_ALLOWED', $data->transaction_day);
            DotenvEditor::setKey('TRANSACTION_ACTIVE_RS_ONLY', $data->transaction_active);
            DotenvEditor::setKey('TRANSACTION_CHUNK', $data->transaction_chunk);

            DotenvEditor::setKey('CODE_BERSIH', $data->code_bersih);
            DotenvEditor::setKey('CODE_KOTOR', $data->code_kotor);
            DotenvEditor::setKey('CODE_RETUR', $data->code_retur);
            DotenvEditor::setKey('CODE_REWASH', $data->code_rewash);

            DotenvEditor::setKey('TELESCOPE_ENABLED', $data->telescope_enable);
            if ($data->has('logo')) {
                $file_logo = $data->file('logo');
                $extension = $file_logo->extension();
                $name = 'logo.' . $extension;
                // $name = time().'.'.$name;

                $file_logo->storeAs('/public/', $name);
                DotenvEditor::setKey('APP_LOGO', $name);
            }

            Alert::update();

        } catch (\Throwable $th) {
            Alert::error($th->getMessage());
            return $th->getMessage();
        }

        return $check;
    }
}
