<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;

class BarcodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            RFID => 'required|array',
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->rfid);

        $rfid = Detail::whereIn(Detail::field_primary(), $this->rfid)->count();
        $compare = $total != $rfid;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'RFID tidak ditemukan!');
            }
        });

        if ($compare) {
            return;
        }

        $check = Transaksi::whereIn(Transaksi::field_rfid(), $this->rfid)
            ->where(Transaksi::field_rs_id(), $this->rs_id)
            ->whereNull(Transaksi::field_barcode())->count();

        $validate = $total != $check;

        $validator->after(function ($validator) use ($validate) {
            if ($validate) {
                $validator->errors()->add('rfid', 'RFID sudah di barcode!');
            }
        });

        if ($validate) {
            return;
        }

        $validator->after(function ($validator) use ($total) {
            $maksimal = env('TRANSACTION_BARCODE_MAXIMAL', 10);
            if ($total > $maksimal) {
                $validator->errors()->add('rfid', 'RFID maksimal ' . $maksimal);
            }
        });
    }

}
