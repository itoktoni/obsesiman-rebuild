<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Attributes\Description;

class CuciType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Unknown          =  0;
    const Cuci              =  1;
    #[Description('Rental')]
    const Sewa              =  2;

}
