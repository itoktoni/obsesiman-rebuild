<?php

namespace App\Dao\Traits;

Trait StatusTrait
{
    public static function getOptions($value = false): array
    {
        $collect = collect(self::getInstances());

        if ($value && is_array($value))
        {
            $collect = $collect->whereIn('value', $value);
        }
        else if ($value && is_integer($value))
        {
            $collect = $collect->where('value', $value);
        }

        $data = [];
        foreach($collect as $item){
            $data[$item->value] = $item->description;
        }

        return $data;
    }
}
