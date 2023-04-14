<?php

namespace App\Dao\Entities;

trait RsEntity
{
    public static function field_has_ruangan()
    {
        return 'has_ruangan';
    }

    public static function field_primary()
    {
        return 'rs_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'rs_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'rs_deskripsi';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_alamat()
    {
        return 'rs_alamat';
    }

    public function getFieldAlamatAttribute()
    {
        return $this->{$this->field_alamat()};
    }

    public static function field_harga_cuci()
    {
        return 'rs_harga_cuci';
    }

    public function getFieldHargaCuciAttribute()
    {
        return $this->{$this->field_harga_cuci()};
    }

    public static function field_harga_sewa()
    {
        return 'rs_harga_sewa';
    }

    public function getFieldHargaSewaAttribute()
    {
        return $this->{$this->field_harga_sewa()};
    }

    public static function field_active()
    {
        return 'rs_aktif';
    }

    public function getFieldActiveAttribute()
    {
        return $this->{$this->field_active()};
    }

}
