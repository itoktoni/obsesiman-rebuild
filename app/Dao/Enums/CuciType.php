<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class CuciType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unassign          =  0;
    const Cuci              =  1;
    const Sewa              =  2;
}
