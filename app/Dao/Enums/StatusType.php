<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class StatusType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const UnAssign                  =  0;
    const Register                  =  1;
    const GantiRfid                 =  2;
    const UpdateRfid                =  3;
    const Kotor                     =  4;
}
