<?php

namespace App\Http\Requests;

use App\Dao\Enums\OpnameType;
use App\Dao\Models\Opname;
use App\Dao\Models\OpnameDetail;
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

    public function withValidator($validator)
    {
        $opname = Opname::find($this->opname_id);
        if ($opname) {
            $validator->after(function ($validator) use($opname) {
                if($opname->opname_status == OpnameType::Selesai){
                    $validator->errors()->add('opname_id', 'Opname telah selesai');
                }
            });
        }
    }

    public function prepareForValidation()
    {
        $send = [];
        $data = OpnameDetail::whereIn(OpnameDetail::field_rfid(), $this->rfid)
            ->where(OpnameDetail::field_opname(), $this->opname_id)
            ->get()
            ->mapWithKeys(function($item){
                return [$item->opname_detail_rfid => $item];
            });

        $send['opname'] = $data;
        $send['rfid'] = collect($this->rfid)->unique()->toArray();
        $send['code'] = $this->code;

        $this->merge([
            'data' => $send
        ]);
    }

}
