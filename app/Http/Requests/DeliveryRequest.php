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
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        //RFID HARUS SUDAH DI BARCODE

        $transaksi = $this->status_transaksi;

        if ($transaksi == TransactionType::BersihKotor) {
            $transaksi = TransactionType::Kotor;
        } else if ($transaksi == TransactionType::BersihRetur){
            $transaksi = TransactionType::Retur;
        } else if ($transaksi == TransactionType::BersihRewash){
            $transaksi = TransactionType::Rewash;
        } else if ($transaksi == TransactionType::Unknown){
            $transaksi = TransactionType::Register;
        }

        $empty = Detail::where(Detail::field_rs_id(), $this->rs_id)
            ->where(Detail::field_status_transaction(), $transaksi)
            ->where(Detail::field_status_process(), ProcessType::Barcode)
            ->count();

        $validator->after(function ($validator) use ($empty) {
            if ($empty == 0) {
                $validator->errors()->add('rfid', 'RFID tidak valid !');
            }
        });

        if ($empty == 0) {
            return;
        }
    }

    public function prepareForValidation()
    {
        $code = '';

        switch ($this->status_transaksi) {
            case TransactionType::BersihKotor:
                $code = env('CODE_BERSIH', 'BSH');
                break;
            case TransactionType::BersihRetur:
                $code = env('CODE_RETUR', 'RTR');
                break;
            case TransactionType::BersihRewash:
                $code = env('CODE_REWASH', 'WSH');
                break;
            default:
                $code = env('CODE_BERSIH', 'BSH');
                break;
        }

        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_delivery(), $code . date('Ymd'), env('AUTO_NUMBER', 15));

        $this->merge([
            'code' => $autoNumber,
        ]);
    }

}
