<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

class ReportType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Pdf       =  1;
    const Html      =  2;
}
