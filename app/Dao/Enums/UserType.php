<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class UserType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const FromUser               =  1;
    const CustomField            =  2;
}
