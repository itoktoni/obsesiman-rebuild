<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\KategoriEntity;
use App\Dao\Entities\OpnameDetailEntity;
use App\Dao\Traits\ActiveTrait;
use App\Dao\Traits\ApiTrait;
use App\Dao\Traits\DataTableTrait;
use App\Dao\Traits\OptionTrait;
use App\Http\Resources\GeneralResource;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use Kyslik\ColumnSortable\Sortable;
use Mehradsadeghi\FilterQueryString\FilterQueryString as FilterQueryString;
use Touhidurabir\ModelSanitize\Sanitizable as Sanitizable;
use Wildside\Userstamps\Userstamps;

class OpnameDetail extends Model
{
    use FilterQueryString, Sanitizable, DataTableTrait, OpnameDetailEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait, Userstamps;

    protected $table = 'opname_detail';
    protected $primaryKey = 'opname_detail_id';

    protected $fillable = [
        'opname_detail_id',
        'opname_detail_id_opname',
        'opname_detail_rfid',
        'opname_detail_waktu',
        'opname_detail_status',
        'opname_detail_transaksi',
        'opname_detail_proses',
        'opname_detail_ketemu',
        'opname_detail_created_at',
        'opname_detail_updated_at',
        'opname_detail_created_by',
        'opname_detail_updated_by',
    ];

    protected $casts = [
        'opname_detail_rfid' => 'string',
        'opname_detail_status' => 'int',
        'opname_detail_transaksi' => 'int',
        'opname_detail_proses' => 'int',
        'opname_detail_ketemu' => 'int',
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = true;
    public $incrementing = true;

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->width(20)->sort(),
            DataBuilder::build($this->field_name())->name('Nama Kategori Linen')->show()->sort(),
            DataBuilder::build($this->field_description())->name('Deskripsi')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function has_view()
    {
        return $this->hasOne(ViewDetailLinen::class, ViewDetailLinen::field_primary(), self::field_rfid());
    }
}
