<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class DeliveryReportRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            'start_delivery' => 'required',
            'end_delivery' => 'required',
            'rs_id' => 'required',
        ];
    }

}
