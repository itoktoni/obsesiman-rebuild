<?php

namespace App\Http\Requests;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TransactionReportRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            'start_date' => 'required',
            'end_date' => 'required',
            // 'rs_id' => 'required',
        ];
    }

}
