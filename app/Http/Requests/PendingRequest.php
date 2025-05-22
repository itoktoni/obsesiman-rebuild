<?php

namespace App\Http\Requests;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Plugins\Query;
use Illuminate\Support\Str;

class PendingRequest extends FormRequest
{
    public function rules()
    {
        return [
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
            'tanggal' => 'required|date_format:Y-m-d',
        ];
    }

    public function withValidator($validator)
    {
        // CASE KETIKA RFID TIDAK DITEMUKAN

        $where = TransactionType::Register;

        /*
        notes : status transaksi berasal dari menu desktop
        */
        if ($this->status_transaksi == TransactionType::BersihKotor) {
            $where = TransactionType::Kotor;
        } else if($this->status_transaksi == TransactionType::BersihRetur) {
            $where = TransactionType::Retur;
        } else if($this->status_transaksi == TransactionType::BersihRewash) {
            $where = TransactionType::Rewash;
        } elseif ($this->status_transaksi == TransactionType::Kotor) {
            $where = TransactionType::Kotor;
        } else if($this->status_transaksi == TransactionType::Retur) {
            $where = TransactionType::Retur;
        } else if($this->status_transaksi == TransactionType::Rewash) {
            $where = TransactionType::Rewash;
        }

         // CASE TRANSAKSI TIDAK ADA YANG PENDING

        $total = Transaksi::select(Transaksi::field_rfid())
            ->whereNotNull(Transaksi::field_barcode())
            ->whereNotNull(Transaksi::field_pending_in())
            ->whereNull(Transaksi::field_pending_out())
            ->where(Transaksi::field_status_transaction(), $where)
            ->where(Transaksi::field_rs_ori(), $this->rs_id)
            ->count();

        $validator->after(function ($validator) use ($total) {
            if ($total == 0) {
                $validator->errors()->add('rfid', 'tidak ada RFID yang Pending !');
            }
        });
    }

    public function prepareForValidation()
    {
        $code = '';

        switch ($this->status_transaksi) {
            case TransactionType::BersihKotor:
                $code = env('CODE_PENDING_BERSIH', 'PBSH');
                break;
            case TransactionType::BersihRetur:
                $code = env('CODE_PENDING_RETUR', 'PRTR');
                break;
            case TransactionType::BersihRewash:
                $code = env('CODE_PENDING_REWASH', 'PWSH');
                break;
            case TransactionType::Register:
                    $code = env('CODE_PENDING_REGISTER', 'PBRU');
                    break;
            default:
                $code = env('CODE_PENDING_BERSIH', 'PBSH');
                break;
        }

        $code_rs = Rs::find($this->rs_id)->rs_code;

        $user = auth()->user()->id;
        $code = 'P'.$code.'-'.$code_rs.'-'.$user.date('ymd');

        //BBSH-RSSC-1092411021
        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_delivery(), $code, 21);

        $this->merge([
            'code' => $autoNumber,
        ]);
    }

}
