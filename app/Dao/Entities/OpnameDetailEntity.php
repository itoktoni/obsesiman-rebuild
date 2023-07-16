<?php

namespace App\Dao\Entities;

use App\Dao\Enums\OpnameType;
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

    public static function field_code()
    {
        return 'opname_detail_code';
    }

    public function getFieldCodeAttribute()
    {
        return $this->{$this->field_code()};
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
        return OpnameType::getDescription($this->{$this->field_status()});
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

    public static function field_created_at()
    {
        return 'opname_detail_created_at';
    }

    public static function field_updated_at()
    {
        return 'opname_detail_updated_at';
    }

    public static function field_updated_by()
    {
        return 'opname_detail_updated_by';
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{$this->field_created_at()};
    }

    public function getFieldUpdatedAtAttribute()
    {
        return $this->{$this->field_updated_at()};
    }

    public function getFieldUpdatedByAttribute()
    {
        return $this->{$this->field_updated_by()};
    }

    public static function field_ketemu()
    {
        return 'opname_detail_ketemu';
    }

    public function getFieldKetemuAttribute()
    {
        return $this->{$this->field_ketemu()};
    }


}
