<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linen extends Model
{
    use HasFactory;

    protected $table = 'item_linen';
    protected $primaryKey = 'item_linen_rfid';
    protected $keyType = 'string';

}
