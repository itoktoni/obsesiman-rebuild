<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewDetailLinenEntity;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class ViewDetailLinen extends Model
{
    use ViewDetailLinenEntity, FilterQueryString, PowerJoins;

    protected $table = 'view_detail_linen';
    protected $primaryKey = 'view_linen_id';

    protected $casts = [
        'view_linen_id' => 'string',
        'view_pemakaian' => 'integer',
    ];

    protected $filters = [
        'filter',
        'view_rs_id',
    ];

    protected $dates = [
        'view_tanggal_create',
        'view_tanggal_update',
        'view_tanggal_delete',
    ];

    public function has_category()
    {
        return $this->hasOne(Kategori::class, Kategori::field_primary(), self::field_category_id());
    }

    public function has_bersih()
    {
        return $this->hasOne(ViewTransaksiBersih::class, ViewTransaksiBersih::field_primary(), self::field_primary());
    }

    public function has_retur()
    {
        return $this->hasOne(ViewTransaksiRetur::class, ViewTransaksiRetur::field_primary(), self::field_primary());
    }

    public function has_rewash()
    {
        return $this->hasOne(ViewTransaksiRewash::class, ViewTransaksiRewash::field_primary(), self::field_primary());
    }
}
