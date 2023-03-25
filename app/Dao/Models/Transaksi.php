<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\DetailEntity;
use App\Dao\Enums\StatusType;
use App\Dao\Enums\StockType;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Plugins\History;
use App\Dao\Models\History as HistoryModel;
use Plugins\Query;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class Transaksi extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, DetailEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait, Userstamps, SoftDeletes;

    protected $table = 'detail';
    protected $primaryKey = 'detail_rfid';

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
    ];

    public $sortable = [
        'detail_nama',
        'detail_deskripsi',
    ];

    protected $casts = [
        'detail_rfid' => 'string',
    ];

    protected $filters = [
        'filter',
    ];

    const CREATED_AT = 'detail_created_at';
    const UPDATED_AT = 'detail_updated_at';
    const DELETED_AT = 'detail_deleted_at';

    const CREATED_BY = 'detail_created_by';
    const UPDATED_BY = 'detail_updated_by';
    const DELETED_BY = 'detail_deleted_by';

    public $timestamps = true;
    public $incrementing = false;

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('RFID')->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build(Ruangan::field_name())->name('Ruangan')->show()->sort(),
            DataBuilder::build($this->field_name())->name('Name')->show()->sort(),
            DataBuilder::build($this->field_stock_status())->name('Stock')->show()->sort(),
            DataBuilder::build($this->field_last_status())->name('Terakhir')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_jenis()
    {
        return $this->hasOne(Jenis::class, Jenis::field_primary(), self::field_name_id());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_id_ruangan());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_id_rs());
    }

    public function has_history()
    {
        return $this->hasMany(HistoryModel::class, HistoryModel::field_name(), self::field_primary());
    }

    public static function boot()
    {
        parent::creating(function($model){

            $model->{$model->field_stock_status()} = StockType::Unassign;
            $model->{$model->field_last_status()} = StatusType::Register;

            History::log($model->field_primary, StatusType::Register, $model->toArray());

        });

        parent::saving(function ($model) {


        });

        parent::boot();
    }
}
