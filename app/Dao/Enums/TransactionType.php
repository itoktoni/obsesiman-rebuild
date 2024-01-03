<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Attributes\Description;

class TransactionType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const NotSet = null;
    const Unknown = 0;
    const Kotor = 1;
    const Retur = 2;
    const Rewash = 3;
    const BersihKotor = 4;
    const BersihRetur = 5;
    const BersihRewash = 6;
    const Register = 7;
    const Exist = 10;

    public static function getDescription($value): string
    {
        if ($value === self::NotSet) {
            return '';
        }

        return parent::getDescription($value);
    }
}
