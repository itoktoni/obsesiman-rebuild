<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RekapReportRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            'start_rekap' => 'required',
            'end_rekap' => 'required',
            'rs_id' => 'required',
        ];
    }

}
