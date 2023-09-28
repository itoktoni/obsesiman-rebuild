<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class SyncType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unknown               =  2;
    const Yes                   =  1;
    const No                    =  0;
}
