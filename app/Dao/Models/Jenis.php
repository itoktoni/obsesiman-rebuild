<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\JenisEntity;
use App\Dao\Entities\NamaLinenEntity;
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

class Jenis extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, JenisEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait, PowerJoins;

    protected $table = 'jenis';
    protected $primaryKey = 'jenis_id';

    protected $fillable = [
        'jenis_id',
        'jenis_id_rs',
        'jenis_id_kategori',
        'jenis_nama',
        'jenis_deskripsi',
        'jenis_gambar',
        'jenis_parstok',
        'jenis_berat',
    ];

    public $sortable = [
        'jenis_nama',
        'jenis_deskripsi',
    ];

    protected $casts = [
        'jenis_id' => 'integer',
        'jenis_id_rs' => 'integer',
        'jenis_id_kategori' => 'integer',
    ];

    protected $filters = [
        'filter',
        'jenis_parstok',
        'jenis_nama',
        'jenis_id',
        'jenis_id_rs',
        'jenis_id_kategori',
    ];

    protected $with = ['has_category'];

    public $timestamps = false;
    public $incrementing = true;

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->show(false)->width(20)->sort(),
            DataBuilder::build(Rs::field_name())->name('Rumah Sakit')->show()->sort(),
            DataBuilder::build(Kategori::field_name())->name('Kategori')->width('100px')->show()->sort(),
            DataBuilder::build($this->field_name())->name('Nama Linen')->width('200px')->show()->sort(),
            DataBuilder::build($this->field_weight())->name('Berat')->show()->sort(),
            DataBuilder::build($this->field_parstock())->name('Parstok')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }

    public function jenis_nama($query, $value)
    {
        return $query->where($this->field_name(), 'like', '%'.$value.'%');
    }

    public function rs_nama($query, $value)
    {
        return $query->where(Rs::field_name(), 'like', '%'.$value.'%');
    }

    public function has_category()
    {
        return $this->hasOne(Kategori::class, Kategori::field_primary(), self::field_category_id());
    }

    public function has_rs()
    {
        return $this->hasOne(Rs::class, Rs::field_primary(), self::field_rs_id());
    }

    public function has_detail()
    {
        return $this->hasMany(Detail::class, Detail::field_jenis_id(), self::field_rs_id());
    }

    public function has_total()
    {
        return $this->hasOne(ViewTotalJenis::class, ViewTotalJenis::field_primary(), self::field_primary());
    }

    public static function boot()
    {
        parent::saving(function ($model) {

            if (request()->has('upload')) {
                $file_upload = request()->file('upload');
                $extension = $file_upload->extension();
                $name = time().'.'.$extension;

                $file_upload->storeAs('/public/jenis/', $name);

                $model->{$model->field_image()} = $name;
            }

        });

        parent::boot();
    }

}
