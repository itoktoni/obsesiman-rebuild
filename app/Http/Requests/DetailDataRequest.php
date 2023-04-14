<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Models\Ruangan;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class DetailDataRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            RFID => 'required',
        ];
    }
}
