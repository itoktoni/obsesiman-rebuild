<?php

namespace App\Dao\Entities;

trait ViewTotalJenisEntity
{
    public static function field_primary()
    {
        return 'view_jenis_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_total()
    {
        return 'view_jenis_total';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_total()};
    }

}
