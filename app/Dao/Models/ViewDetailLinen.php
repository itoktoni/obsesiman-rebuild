<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewDetailLinenEntity;
use Illuminate\Database\Eloquent\Model;

class ViewDetailLinen extends Model
{
    use ViewDetailLinenEntity;

    protected $table = 'view_detail_linen';
    protected $primaryKey = 'view_linen_id';

    protected $casts = [
        'view_linen_id' => 'string',
        'view_pemakaian' => 'integer',
    ];
}
