<?php

namespace App\Http\Requests;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Plugins\Query;

class DeliveryRequest extends FormRequest
{
    public function rules()
    {
        return [
            'barcode' => 'required|array',
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->barcode);

        //RFID HARUS SUDAH DI BARCODE
        $empty = Detail::where(Detail::field_rs_id(), $this->rs_id)
            ->where(Detail::field_status_process(), ProcessType::Barcode)
            ->count();

        $validator->after(function ($validator) use ($empty) {
            if ($empty == 0) {
                $validator->errors()->add('rfid', 'RFID tidak ditemukan!');
            }
        });

        if ($empty == 0) {
            return;
        }

        $barcode = Transaksi::with(['has_rfid' => function($query){
            $query->where(Detail::field_rs_id(), $this->rs_id);
        }])->whereIn(Transaksi::field_barcode(), $this->barcode)
                    ->count();

        $compare = $total != $barcode;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'konfigurasi Barcode tidak cocok!');
            }
        });

        if ($compare) {
            return;
        }
    }

    public function prepareForValidation()
    {
        $code = '';

        switch ($this->status_transaksi) {
            case TransactionType::BersihKotor:
                $code = env('CODE_BERSIH', 'BRS');
                break;
            case TransactionType::BersihRetur:
                $code = env('CODE_RETUR', 'RTR');
                break;
            case TransactionType::BersihRewash:
                $code = env('CODE_REWASH', 'WSH');
                break;
            default:
                $code = env('CODE_BERSIH', 'KTR');
                break;
        }

        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_delivery(), $code . date('Ymd'), env('AUTO_NUMBER', 15));

        $this->merge([
            'code' => $autoNumber,
        ]);
    }

}
