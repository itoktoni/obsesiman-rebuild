<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Models\Ruangan;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RegisterRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            RFID => 'required',
            RS_ID => 'required',
            RUANGAN_ID => 'required',
            JENIS_ID => 'required',
            STATUS_CUCI => 'required|in:0,1,2',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_rs_id() =>  $this->rs_id,
            Detail::field_ruangan_id() =>  $this->ruangan_id,
            Detail::field_jenis_id() =>  $this->jenis_id,
            Detail::field_status_cuci() =>  $this->status_cuci,
        ]);
    }

}
