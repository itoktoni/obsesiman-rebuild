<?php

namespace App\Dao\Entities;

use App\Dao\Enums\StatusType;
use App\Dao\Enums\StockType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;

trait OpnameDetailEntity
{
    public static function field_primary()
    {
        return 'opname_detail_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_opname()
    {
        return 'opname_detail_id_opname';
    }

    public function getFieldOpnameAttribute()
    {
        return $this->{$this->field_opname()};
    }

    public static function field_rfid()
    {
        return 'opname_detail_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{$this->field_rfid()};
    }

    public static function field_waktu()
    {
        return 'opname_detail_waktu';
    }

    public function getFieldWaktuAttribute()
    {
        return $this->{$this->field_waktu()};
    }

    public static function field_status()
    {
        return 'opname_detail_status';
    }

    public function getFieldStatusAttribute()
    {
        return StatusType::getDescription($this->{$this->field_status()});
    }

    public static function field_transaksi()
    {
        return 'opname_detail_transaksi';
    }

    public function getFieldTransaksiAttribute()
    {
        return TransactionType::getDescription($this->{$this->field_transaksi()});
    }

    public static function field_proses()
    {
        return 'opname_detail_proses';
    }

    public function getFieldProsesAttribute()
    {
        return TransactionType::getDescription($this->{$this->field_proses()});
    }

    public static function field_name()
    {
        return 'opname_detail_rfid';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rs_id()
    {
        return 'opname_detail_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{self::CREATED_AT};
    }

    public function getFieldUpdatedAtAttribute()
    {
        return $this->{self::UPDATED_AT};
    }


}
