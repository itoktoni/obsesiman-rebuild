<?php

namespace App\Dao\Models;

use App\Dao\Entities\ViewTransaksiCuciEntity;
use Illuminate\Database\Eloquent\Model;

class ViewTransaksiCuci extends Model
{
    use ViewTransaksiCuciEntity;

    protected $table = 'view_transaksi_cuci';
    protected $primaryKey = 'transaksi_cuci_id';

    protected $casts = [
        'transaksi_cuci_id' => 'integer',
        'transaksi_cuci_total' => 'integer',
    ];
}
