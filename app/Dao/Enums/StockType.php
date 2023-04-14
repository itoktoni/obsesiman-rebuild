<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class StockType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Rs               =  1;
    const Laundry          =  2;
    const Unknown         =  0;
}
