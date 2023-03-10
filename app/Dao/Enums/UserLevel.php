<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;

class UserLevel extends Enum implements LocalizedEnum
{
    use StatusTrait;

    const Pengguna = 1;
    const Teknisi = 2;
    const Admin = 50;
    const Developer = 100;
}
