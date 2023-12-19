<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Attributes\Description;

class FilterType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Kotor = 1;
    const Retur = 2;
    const Rewash = 3;
    const ScanRs = 20;
    const Pending = 70;
    const Hilang = 80;
    const BelumRegister = 100;

}
