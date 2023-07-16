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
        $data = Detail::whereIn(Detail::field_primary(), $this->rfid)
            ->get();

            if(!empty($data)){
                $rfid = $data
                ->mapWithKeys(function($item){
                    $data = [
                        OpnameDetail::field_code() => $this->code,
                        OpnameDetail::field_transaksi() => $item->field_status_transaction,
                        OpnameDetail::field_proses() => $item->field_status_process,
                        OpnameDetail::field_ketemu() => BooleanType::Yes,
                        OpnameDetail::field_updated_at() => date('Y-m-d H:i:s'),
                        OpnameDetail::field_updated_by() => auth()->user()->id,
                    ];

                    return [$item->field_primary => $data];
                })->toArray();
            } else{
                $rfid = [];
            }

        $this->merge([
            'data' => $rfid
        ]);
    }

}
