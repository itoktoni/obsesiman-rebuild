<?php

namespace App\Http\Requests;

use App\Dao\Enums\BooleanType;
use App\Dao\Enums\OpnameType;
use App\Dao\Models\Detail;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
use App\Dao\Models\Rs;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OpnameDetailRequest extends FormRequest
{
    use ValidationTrait;

    public function validation() : array
    {
        return [
            Opname::field_primary() => 'required',
            'code' => 'required',
            'rfid' => 'required|array',
        ];
    }

    public function prepareForValidation()
    {
        $send = [];
        $data = Detail::whereIn(Detail::field_primary(), $this->rfid)
            ->get();

            if(!empty($data)){
                $rfid = $data
                ->mapWithKeys(function($item){
                    return [$item->field_primary => $item];
                });
            } else{
                $rfid = [];
            }

        $send['data'] = $rfid;
        $send['rfid'] = $this->rfid;

        $this->merge([
            'data' => $send
        ]);
    }

}
