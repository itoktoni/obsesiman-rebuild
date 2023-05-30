<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\TransaksiEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use App\Dao\Models\History as HistoryModel;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class Transaksi extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, TransaksiEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait, Userstamps;

    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';

    protected $fillable = [
        'transaksi_id',
        'transaksi_key',
        'transaksi_status',
        'transaksi_rfid',
        'transaksi_report',
        'transaksi_barcode',
        'transaksi_delivery',
        'transaksi_beda_rs',
        'transaksi_id_rs',
        'transaksi_created_at',
        'transaksi_updated_at',
        'transaksi_created_by',
        'transaksi_updated_by',
        'transaksi_deleted_at',
        'transaksi_deleted_by',
        'transaksi_barcode_at',
        'transaksi_barcode_by',
        'transaksi_delivery_at',
        'transaksi_delivery_by',
    ];

    public $sortable = [
        'transaksi_key',
    ];

    protected $casts = [
        'transaksi_rfid' => 'string',
        'transaksi_status' => 'integer',
    ];

    protected $filters = [
        'filter',
        'transaksi_id_rs',
        'transaksi_status',
        'transaksi_created_by',
        'rs_id'
    ];

    const CREATED_AT = 'transaksi_created_at';
    const UPDATED_AT = 'transaksi_updated_at';
    const DELETED_AT = 'transaksi_deleted_at';

    const CREATED_BY = 'transaksi_created_by';
    const UPDATED_BY = 'transaksi_updated_by';
    const DELETED_BY = 'transaksi_deleted_by';

    public $timestamps = true;
    public $incrementing = false;

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('Nomer Transaksi')->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_detail()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_rfid());
    }

    public function has_cuci()
    {
        return $this->hasOne(ViewTransaksiCuci::class, ViewTransaksiCuci::field_primary(), self::field_rfid());
    }

    public function has_retur()
    {
        return $this->hasOne(ViewTransaksiRetur::class, ViewTransaksiRetur::field_primary(), self::field_rfid());
    }

    public function has_rewash()
    {
        return $this->hasOne(ViewTransaksiRewash::class, ViewTransaksiRewash::field_primary(), self::field_rfid());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_user()
    {
        return $this->hasOne(User::class, User::field_primary(), self::field_created_by());
    }

    public function has_history()
    {
        return $this->hasMany(HistoryModel::class, HistoryModel::field_name(), self::field_primary());
    }
}
