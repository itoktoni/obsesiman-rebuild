<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class ProcessType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unknown                  =  0;
    const Register                  =  1;
    const GantiChip                 =  2;
    const UpdateChip                =  3;
    const DeleteChip                =  4;
    const Kotor                     =  10;
    const Bersih                     =  20;
    const DeleteTransaksi            =  30;
}
