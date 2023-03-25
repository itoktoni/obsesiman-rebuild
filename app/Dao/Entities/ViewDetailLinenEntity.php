<?php

namespace App\Dao\Entities;

use App\Dao\Enums\CuciType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\RegisterType;
use App\Dao\Enums\TransactionType;

trait ViewDetailLinenEntity
{
    public static function field_primary()
    {
        return 'view_linen_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'view_linen_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rs_id()
    {
        return 'view_rs_id';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_rs_name()
    {
        return 'view_rs_nama';
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{$this->field_rs_name()};
    }

    public static function field_ruangan_id()
    {
        return 'view_ruangan_id';
    }

    public function getFieldRuanganIdAttribute()
    {
        return $this->{$this->field_ruangan_id()};
    }

    public static function field_ruangan_name()
    {
        return 'view_ruangan_nama';
    }

    public function getFieldRuanganNameAttribute()
    {
        return $this->{$this->field_ruangan_name()};
    }

    public static function field_status_register()
    {
        return 'view_status_register';
    }

    public function getFieldStatusRegisterAttribute()
    {
        return $this->{$this->field_status_register()};
    }

    public function getFieldStatusRegisterNameAttribute()
    {
        return RegisterType::getDescription($this->getFieldStatusRegisterAttribute());
    }

    public static function field_status_cuci()
    {
        return 'view_status_cuci';
    }

    public function getFieldStatusCuciAttribute()
    {
        return $this->{$this->field_status_cuci()};
    }

    public function getFieldStatusCuciNameAttribute()
    {
        return CuciType::getDescription($this->getFieldStatusCuciAttribute());
    }

    public static function field_status_trasaction()
    {
        return 'view_status_transaksi';
    }

    public function getFieldStatusTransactionAttribute()
    {
        return $this->{$this->field_status_trasaction()};
    }

    public function getFieldStatusTransactionNameAttribute()
    {
        return TransactionType::getDescription($this->getFieldStatusTransactionAttribute());
    }

    public static function field_status_process()
    {
        return 'view_status_proses';
    }

    public function getFieldStatusProcessAttribute()
    {
        return $this->{$this->field_status_process()};
    }

    public function getFieldStatusProcessNameAttribute()
    {
        return ProcessType::getDescription($this->getFieldStatusProcessAttribute());
    }

    public static function field_tanggal_update()
    {
        return 'view_tanggal_update';
    }

    public function getFieldTanggalUpdateAttribute()
    {
        return $this->{$this->field_tanggal_update()};
    }

    public static function field_pemakaian()
    {
        return 'view_pemakaian';
    }

    public function getFieldPemakaianAttribute()
    {
        return $this->{$this->field_pemakaian()};
    }

}
