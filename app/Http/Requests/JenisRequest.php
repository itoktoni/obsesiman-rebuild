<?php

namespace App\Http\Requests;

use App\Dao\Models\Jenis;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class JenisRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            Jenis::field_name() => 'required',
            Jenis::field_category_id() => 'required',
            Jenis::field_rs_id() => 'required',
            Jenis::field_weight() => 'required|numeric',
            Jenis::field_parstock() => 'required|numeric',
            UPLOAD => 'image',
        ];
    }
}
