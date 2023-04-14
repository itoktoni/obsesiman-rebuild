<?php

namespace App\Dao\Entities;

trait ViewInvoiceEntity
{
    public static function field_primary()
    {
        return 'inv_key';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'inv_nama_linen';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_rs()
    {
        return 'inv_nama_rs';
    }

    public function getFieldRsAttribute()
    {
        return $this->{$this->field_rs()};
    }

    public static function field_tanggal()
    {
        return 'inv_tanggal';
    }

    public function getFieldTanggalAttribute()
    {
        return $this->{$this->field_tanggal()};
    }

    public static function field_qty()
    {
        return 'inv_qty';
    }

    public function getFieldQtyAttribute()
    {
        return $this->{$this->field_qty()};
    }

    public static function field_berat()
    {
        return 'inv_berat';
    }

    public function getFieldBeratAttribute()
    {
        return $this->{$this->field_berat()};
    }

    public static function field_total_berat()
    {
        return 'inv_total_berat';
    }

    public function getFieldTotalBeratAttribute()
    {
        return $this->{$this->field_total_berat()};
    }

    public static function field_harga()
    {
        return 'inv_harga';
    }

    public function getFieldHargaAttribute()
    {
        return $this->{$this->field_harga()};
    }

    public static function field_invoice()
    {
        return 'inv_total_invoice';
    }

    public function getFieldInvoiceAttribute()
    {
        return $this->{$this->field_invoice()};
    }

    public static function field_reported_at()
    {
        return 'inv_tanggal';
    }

    public function getFieldReportedAtAttribute()
    {
        return $this->{$this->field_reported_at()};
    }
}
