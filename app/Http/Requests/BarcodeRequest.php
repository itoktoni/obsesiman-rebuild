<?php

namespace App\Http\Requests;

use App\Dao\Models\Transaksi;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class BarcodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            RFID => 'required',
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->rfid);

        $check = Transaksi::whereIn(Transaksi::field_rfid(), $this->rfid)
        ->where(Transaksi::field_status_transaction(), $this->status_transaksi)
        ->where(Transaksi::field_rs_id(), $this->rs_id)
        ->whereNull(Transaksi::field_barcode())->count();

        $validate = $total != $check;

        $validator->after(function ($validator) use ($total) {
            $maksimal = env('TRANSACTION_BARCODE_MAXIMAL', 10);
            if($total > $maksimal){
                $validator->errors()->add('rfid', 'RFID maksimal '.$maksimal);
            }
        });

        $validator->after(function ($validator) use ($validate) {
            if($validate){
                $validator->errors()->add('rfid', 'RFID tidak ditemukan!');
            }
        });
    }

}
