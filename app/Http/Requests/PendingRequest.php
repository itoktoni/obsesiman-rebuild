<?php

namespace App\Http\Requests;

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
            RFID => 'required|array',
            RS_ID => 'required',
            STATUS_TRANSAKSI => 'required',
            'tanggal' => 'required|date_format:Y-m-d',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->rfid);

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

        $rfid = Detail::whereIn(Detail::field_primary(), $this->rfid)
                    ->where(Detail::field_status_transaction(), $where)
                    ->where(Detail::field_rs_id(), $this->rs_id);

        $total_rfid_original = $rfid->count();

        $compare = $total != $total_rfid_original;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'RFID tidak sesuai dengan proses !');
            }
        });

        if ($compare) {
            return;
        }

        // CASE YANG DIBARCODE LEBIH DARI YANG DITENTUKAN

        $validator->after(function ($validator) use ($total) {

            $maksimal = env('TRANSACTION_BARCODE_MAXIMAL', 10);
            if ($total > $maksimal) {
                $validator->errors()->add('rfid', 'RFID maksimal ' . $maksimal);
            }
        });

        // CASE PREVENT DATA WHEN RFID PENDING

        $transaksi = Transaksi::select(Transaksi::field_rfid())
            ->whereIn(Transaksi::field_rfid(), $this->rfid)
            ->whereDate(Transaksi::field_pending_in(), $this->tanggal)
            ->whereNull(Transaksi::field_pending_out())
            ->count();

        $compare = $total != $transaksi;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'ADA RFID PENDING YANG TIDAK SESUAI !');
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
        $code = $code.'-'.$code_rs.'-'.$user.date('ymd');

        //BBSH-RSSC-1092411021
        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_delivery(), $code, 20);

        $this->merge([
            'code' => $autoNumber,
        ]);
    }

}
