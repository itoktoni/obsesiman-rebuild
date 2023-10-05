<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class BedaRsType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Sama              =  0;
    const Beda              =  1;
    const BelumRegister     =  2;
}
