<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\KategoriEntity;
use App\Dao\Entities\MutasiEntity;
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

class Mutasi extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, MutasiEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait;

    protected $table = 'mutasi';
    protected $primaryKey = 'mutasi_id';

    protected $fillable = [
        'mutasi_id',
        'mutasi_nama',
        'mutasi_tanggal',
        'mutasi_id_rs',
        'mutasi_id_linen',
        'mutasi_register_real',
        'mutasi_register_calc',
        'mutasi_kotor',
        'mutasi_bersih',
        'mutasi_selisih_minus',
        'mutasi_selisih_plus',
        'mutasi_saldo',
    ];

    public $sortable = [
        'mutasi_nama',
        'mutasi_tanggal',
    ];

    protected $casts = [
        'mutasi_id' => 'integer'
    ];

    protected $filters = [
        'filter',
    ];

    public $timestamps = false;
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
}
