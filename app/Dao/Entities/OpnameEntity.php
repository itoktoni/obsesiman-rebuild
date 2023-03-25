<?php

namespace App\Dao\Entities;

use App\Dao\Models\Rs;

trait OpnameEntity
{
    public static function field_primary()
    {
        return 'opname_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'opname_nama';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_start()}.' '.$this->{$this->field_end()};
    }

    public static function field_start()
    {
        return 'opname_mulai';
    }

    public function getFieldStartAttribute()
    {
        return $this->{$this->field_start()};
    }

    public static function field_end()
    {
        return 'opname_selesai';
    }

    public function getFieldEndAttribute()
    {
        return $this->{$this->field_end()};
    }

    public static function field_id_rs()
    {
        return 'opname_id_rs';
    }

    public function getFieldRsIdAttribute()
    {
        return $this->{$this->field_id_rs()};
    }

    public function getFieldRsNameAttribute()
    {
        return $this->{Rs::field_name()};
    }

}
