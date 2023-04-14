<?php

namespace App\Dao\Entities;

use App\Dao\Enums\ProcessType;
use App\Dao\Enums\StatusType;
use App\Dao\Enums\TransactionType;

trait HistoryEntity
{
    public static function field_primary()
    {
        return 'history_id';
    }

    public function getFieldPrimaryAttribute()
    {
        return $this->{$this->field_primary()};
    }

    public static function field_name()
    {
        return 'history_rfid';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

    public static function field_description()
    {
        return 'history_data';
    }

    public function getFieldDescriptionAttribute()
    {
        return $this->{$this->field_description()};
    }

    public static function field_status()
    {
        return 'history_status';
    }

    public function getFieldStatusAttribute()
    {
        return ProcessType::getDescription($this->{$this->field_status()});
    }

    public static function field_created_at()
    {
        return 'history_waktu';
    }

    public function getFieldCreatedAtAttribute()
    {
        return $this->{$this->field_created_at()};
    }

    public static function field_created_by()
    {
        return 'history_user';
    }

    public function getFieldCreatedByAttribute()
    {
        return $this->{$this->field_created_by()};
    }
}
