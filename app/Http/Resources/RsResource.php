<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public static $model;

    public function toArray($request)
    {
        return [
            'rs_id' => $this->field_primary,
            'rs_nama' => $this->field_name,
            'deskripsi' => $this->field_description,
            'ruangan' => RuanganResource::collection($this->has_ruangan),
            'linen' => JenisResource::collection($this->has_jenis),
        ];
        // return parent::toArray($request);
    }
}
