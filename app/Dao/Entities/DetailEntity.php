<?php

namespace App\Dao\Entities;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\LinenType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use App\Dao\Models\ViewTransaksiCuci;

trait DetailEntity
{
    public static function field_primary()
    {
        return 'detail_rfid';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_jenis_id()
    {
        return 'detail_id_jenis';
    }

    public static function field_name()
    {
        return Jenis::field_name();
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public function getFieldWeightAttribute()
    {
        return $this->{Jenis::field_weight()};
    }

    public static function field_description()
    {
        return 'detail_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_ruangan_id()
    {
        return 'detail_id_ruangan';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public function getFieldRuanganNameAttribute()
    {
        return $this->{Ruangan::field_name()};
    }

    public static function field_rs_id()
    {
        return 'detail_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public static function field_status_cuci()
    {
        return 'detail_status_cuci';
    }

    public function getFieldStatusCuciAttribute()
    {
        return $this->{$this->field_status_cuci()};
    }

    public function getFieldStatusCuciNameAttribute()
    {
        return CuciType::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_total_cuci()
    {
        return ViewTransaksiCuci::field_total();
    }

    public function getFieldTotalCuciAttribute()
    {
        return $this->{$this->field_total_cuci()};
    }

    public static function field_status_transaction()
    {
        return 'detail_status_transaksi';
    }

    public function getFieldStatusTransactionAttribute()
    {
        return $this->{$this->field_status_transaction()};
    }

    public function getFieldStatusTransactionNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusTransactionAttribute());
    }

    public static function field_status_register()
    {
        return 'detail_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{$this->field_status_register()};
    }

    public function getFieldStatusRegisterNameAttribute()
    {
        return RegisterType::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_process()
    {
        return 'detail_status_proses';
    }

    public function getFieldStatusProcessAttribute()
    {
        return $this->{$this->field_status_process()};
    }

    public function getFieldStatusProcessNameAttribute()
    {
        return ProcessType::getDescription($this->getFieldStatusProcessAttribute());
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
