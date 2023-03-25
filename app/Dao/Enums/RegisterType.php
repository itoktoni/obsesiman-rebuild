<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class RegisterType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const GantiChip          =  0;
    const Register           =  1;
}
