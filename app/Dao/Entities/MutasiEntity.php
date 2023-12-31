<?php

namespace App\Dao\Entities;

trait MutasiEntity
{
    public static function field_primary()
    {
        return 'mutasi_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'mutasi_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_tanggal()
    {
        return 'mutasi_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_rs_id()
    {
        return 'mutasi_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_rs_id()};
    }

    public static function field_linen_id()
    {
        return 'mutasi_id_linen';
    }

    public function getFieldLinenIdAttribute()
    {
        return $this->{$this->field_linen_id()};
    }

    public static function field_kotor()
    {
        return 'mutasi_kotor';
    }

    public function getFieldKotorAttribute()
    {
        return $this->{$this->field_kotor()};
    }

    public static function field_bersih()
    {
        return 'mutasi_bersih';
    }

    public function getFieldBersihAttribute()
    {
        return $this->{$this->field_bersih()};
    }

}
