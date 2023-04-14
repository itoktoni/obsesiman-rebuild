<?php

namespace App\Dao\Models;

use App\Dao\Builder\DataBuilder;
use App\Dao\Entities\HistoryEntity;
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

class History extends Model
{
    use Sortable, FilterQueryString, Sanitizable, DataTableTrait, HistoryEntity, ActiveTrait, OptionTrait, PowerJoins, ApiTrait;

    protected $table = 'history';
    protected $primaryKey = 'history_id';

    protected $fillable = [
        'history_id',
        'history_rfid',
        'history_waktu',
        'history_user',
        'history_status',
        'history_data',
    ];

    public $sortable = [
        'history_rfid',
    ];

    protected $casts = [
        'history_rfid' => 'string'
    ];

    protected $filters = [
        'filter',
        'history_status',
    ];

    public $timestamps = false;
    public $incrementing = true;

    public function fieldSearching(){
        return $this->field_name();
    }

    public function fieldDatatable(): array
    {
        return [
            DataBuilder::build($this->field_primary())->name('ID')->show(false)->sort(),
            DataBuilder::build($this->field_name())->name('Nomer Tag RFID')->show()->sort(),
            DataBuilder::build($this->field_status())->name('Status')->show()->sort(),
            DataBuilder::build($this->field_created_at())->name('Waktu')->show()->sort(),
            DataBuilder::build($this->field_created_by())->name('User')->show()->sort(),
        ];
    }

    public function apiTransform()
    {
        return GeneralResource::class;
    }
}
