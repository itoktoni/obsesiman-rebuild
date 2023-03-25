<?php

namespace App\Http\Requests;

use App\Dao\Models\Detail;
use App\Dao\Models\Ruangan;
use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class RegisterRequest extends FormRequest
{
    use ValidationTrait;

    public const rfid = 'rfid';
    public const rs_id = 'rs_id';
    public const ruangan_id = 'ruangan_id';
    public const jenis_id = 'jenis_id';
    public const status_cuci = 'status_cuci';
    public const status_register = 'status_register';

    public function validation() : array
    {
        return [
            self::rfid => 'required',
            self::rs_id => 'required',
            self::ruangan_id => 'required',
            self::jenis_id => 'required',
            self::status_cuci => 'required|in:0,1,2',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            Detail::field_rs_id() =>  $this->rs_id,
            Detail::field_ruangan_id() =>  $this->ruangan_id,
            Detail::field_jenis_id() =>  $this->jenis_id,
            Detail::field_status_cuci() =>  $this->status_cuci,
        ]);
    }

}
