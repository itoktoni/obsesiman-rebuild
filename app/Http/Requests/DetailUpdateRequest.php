<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Models\Ruangan;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class DetailUpdateRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            RegisterRequest::rs_id => 'required',
            RegisterRequest::ruangan_id => 'required',
            RegisterRequest::jenis_id => 'required',
            RegisterRequest::status_cuci => 'in:0,1,2',
            RegisterRequest::status_register => 'in:0,1',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_jenis_id() =>  $this->jenis_id,
            Detail::field_rs_id() =>  $this->rs_id,
            Detail::field_ruangan_id() =>  $this->ruangan_id,
        ]);
    }

}
