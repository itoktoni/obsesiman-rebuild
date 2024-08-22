<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Attributes\Description;

class LogType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const NotSet = null;
    const Unknown = 0;
    const Kotor = 1;
    const Retur = 2;
    const Rewash = 3;
    const Register = 7;

    const UpdateChip = 8;

    const Bersih = 20;
    const Grouping = 30;
    const Barcode = 31;

    public static function getDescription($value): string
    {
        if ($value === self::Kotor) {
            return 'Scan Kotor';
        }

        if ($value === self::Retur) {
            return 'Scan Retur';
        }

        if ($value === self::Rewash) {
            return 'Scan Rewash';
        }

        if ($value === null) {
            return '';
        }

        return parent::getDescription($value);
    }
}
