<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class OpnameType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Draft = 0;
    const Proses              =  1;
    const Selesai              =  2;

}
