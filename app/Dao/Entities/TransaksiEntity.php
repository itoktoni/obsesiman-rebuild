<?php

namespace App\Dao\Entities;

use App\Dao\Enums\BedaRsType;
use App\Dao\Enums\StatusType;
use App\Dao\Enums\StockType;
use App\Dao\Models\Kategori;
use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;

trait TransaksiEntity
{
    public static function field_primary()
    {
        return 'transaksi_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_rfid()
    {
        return 'transaksi_rfid';
    }

    public function getFieldRfidAttribute()
    {
        return $this->{$this->field_rfid()};
    }

    public static function field_key()
    {
        return 'transaksi_key';
    }

    public function getFieldKeyAttribute()
    {
        return $this->{$this->field_key()};
    }

    public static function field_name()
    {
        return Jenis::field_name();
    }

    public static function field_description()
    {
        return 'transaksi_beda_rs';
    }

    public function getFieldDescriptionAttribute()
    {
        return BedaRsType::getDescription($this->{$this->field_description()});
    }

    public static function field_id_rs()
    {
        return 'transaksi_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_id_rs()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

    public static function field_status()
    {
        return 'transaksi_status';
    }

    public function getFieldStatusAttribute()
    {
        return StatusType::getDescription($this->{$this->field_status()});
    }

    public static function field_barcode()
    {
        return 'transaksi_barcode';
    }

    public function getFieldBarcodeAttribute()
    {
        return $this->{$this->field_barcode()};
    }

    public static function field_beda_rs()
    {
        return 'transaksi_beda_rs';
    }

    public function getFieldBedaRsAttribute()
    {
        return $this->{$this->field_beda_rs()};
    }

    public static function field_delivery()
    {
        return 'transaksi_delivery';
    }

    public function getFieldDeliveryAttribute()
    {
        return $this->{$this->field_delivery()};
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
