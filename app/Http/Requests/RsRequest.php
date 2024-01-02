<?php

namespace App\Http\Requests;

use App\Dao\Models\Rs;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class RsRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            Rs::field_name() => 'required',
            // Rs::field_harga_cuci() => 'required|numeric',
            // Rs::field_harga_sewa() => 'required|numeric',
            'ruangan' => 'required',
        ];
    }
}
