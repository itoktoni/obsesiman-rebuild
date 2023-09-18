<?php

namespace App\Http\Requests;

use App\Dao\Enums\TransactionType;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Plugins\Query;

class BarcodeRequest extends FormRequest
{
    public function rules()
    {
        return [
            RFID => 'required|array',
            RS_ID => 'required',
            RUANGAN_ID => 'required',
            STATUS_TRANSAKSI => 'required',
        ];
    }

    public function withValidator($validator)
    {
        $total = count($this->rfid);

        // CASE KETIKA RFID TIDAK DITEMUKAN

        $rfid = Detail::whereIn(Detail::field_primary(), $this->rfid)
                ->where(Detail::field_rs_id(), $this->rs_id);

        $rfid_kotor = clone $rfid;
        $total_rfid_kotor = $rfid_kotor->whereNotIn(Detail::field_status_transaction(), BERSIH)->count();
        $total_rfid_original = $rfid->count();

        $compare = $total != $total_rfid_original;

        $validator->after(function ($validator) use ($compare) {
            if ($compare) {
                $validator->errors()->add('rfid', 'RFID tidak ditemukan!');
            }
        });

        if ($compare) {
            return;
        }

        // CASE KETIKA YANG DITEMBAK RFID YANG BERSIH

        $bersih = $total != $total_rfid_kotor;

        $validator->after(function ($validator) use ($bersih) {
            if ($bersih) {
                $validator->errors()->add('rfid', 'RFID harus melewat grouping!');
            }
        });

        if ($bersih) {
            return;
        }

        // CASE RFID SUDAH DI BARCODE

        $check = Transaksi::whereIn(Transaksi::field_rfid(), $this->rfid)
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

        // CASE YANG DIBARCODE LEBIH DARI YANG DITENTUKAN

        $validator->after(function ($validator) use ($total) {
            $maksimal = env('TRANSACTION_BARCODE_MAXIMAL', 10);
            if ($total > $maksimal) {
                $validator->errors()->add('rfid', 'RFID maksimal ' . $maksimal);
            }
        });

        // CASE KALAU YANG DIPILIH BUKAN TRANSAKSI BERSIH

        $status_transaksi = $this->status_transaksi;

        $validator->after(function ($validator) use ($status_transaksi) {
            if (!in_array($status_transaksi, BERSIH)) {
                $validator->errors()->add('rfid', 'Status Transaksi harus bersih, return atau rewash');
            }
        });
    }

    public function prepareForValidation()
    {
        $code = '';

        switch ($this->status_transaksi) {
            case TransactionType::BersihKotor:
                $code = env('CODE_KOTOR', 'KTR');
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

        $autoNumber = Query::autoNumber(Transaksi::getTableName(), Transaksi::field_barcode(), $code . date('Ymd'), env('AUTO_NUMBER', 15));

        $this->merge([
            'code' => $autoNumber,
        ]);
    }

}
