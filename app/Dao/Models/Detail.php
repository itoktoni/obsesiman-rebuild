<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\DetailEntity;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
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
use PHPUnit\Framework\MockObject\Api;
use Plugins\History;
use App\Dao\Models\History as HistoryModel;
use Plugins\Query;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class Detail extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, DetailEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait, Userstamps, SoftDeletes;

    protected $table = 'detail';
    protected $primaryKey = 'detail_rfid';

    protected $fillable = [
        'detail_rfid',
        'detail_id_rs',
        'detail_id_ruangan',
        'detail_id_jenis',
        'detail_status_cuci',
        'detail_status_transaksi',
        'detail_status_proses',
        'detail_status_register',
        'detail_deskripsi',
        'detail_created_at',
        'detail_updated_at',
        'detail_deleted_at',
        'detail_created_by',
        'detail_updated_by',
        'detail_deleted_by',
    ];

    public $sortable = [
        'detail_nama',
        'detail_deskripsi',
    ];

    protected $casts = [
        'detail_rfid' => 'string',
        'detail_status_proses' => 'integer',
        'detail_status_cuci' => 'integer',
        'detail_status_register' => 'integer',
        'detail_status_transaksi' => 'integer',
    ];

    protected $filters = [
        'filter',
        'detail_status_register',
        'ruangan_nama',
    ];

    protected $dates = [
        SELF::CREATED_AT,
        SELF::UPDATED_AT,
        SELF::DELETED_AT,
    ];

    const CREATED_AT = 'detail_created_at';
    const UPDATED_AT = 'detail_updated_at';
    const DELETED_AT = 'detail_deleted_at';

    const CREATED_BY = 'detail_created_by';
    const UPDATED_BY = 'detail_updated_by';
    const DELETED_BY = 'detail_deleted_by';

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldStatus(): array {
        return [
            $this->field_status_process() => ProcessType::class,
            $this->field_status_register() => RegisterType::class
        ];
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('RFID')->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build(Ruangan::field_name())->name('Ruangan')->show()->sort(),
            DataBuilder::build($this->field_name())->name('Name')->show()->width('200px')->sort(),
            DataBuilder::build(Jenis::field_weight())->name('Berat')->show()->sort(),
            DataBuilder::build($this->field_status_register())->name('Register')->show()->sort(),
            DataBuilder::build($this->field_status_cuci())->name('Cuci')->show()->sort(),
            DataBuilder::build($this->field_status_transaction())->name('Transaksi')->show()->sort(),
            DataBuilder::build($this->field_status_process())->name('Posisi Terakhir')->show()->sort(),
            DataBuilder::build(self::UPDATED_AT)->name('Update Terakhir')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_jenis()
    {
        return $this->hasOne(Jenis::class, Jenis::field_primary(), self::field_jenis_id());
    }

    public function has_ruangan()
    {
        return $this->hasOne(Ruangan::class, Ruangan::field_primary(), self::field_ruangan_id());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_cuci()
    {
        return $this->hasOne(ViewTransaksiCuci::class, ViewTransaksiCuci::field_primary(), self::field_primary());
    }

    public function has_view()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_primary());
    }

    public function has_history()
    {
        return $this->hasMany(HistoryModel::class, HistoryModel::field_name(), self::field_primary());
    }
}
