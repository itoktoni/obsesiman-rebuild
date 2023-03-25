<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class ProcessType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unassign                  =  0;
    const Register                  =  1;
    const GantiChip                 =  2;
    const UpdateRfid                =  3;
    const Kotor                     =  4;
}
