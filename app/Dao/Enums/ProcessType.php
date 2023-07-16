<?php

namespace App\Dao\Enums;

use App\Dao\Traits\StatusTrait;
use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum as Enum;
use BenSampo\Enum\Attributes\Description;

class ProcessType extends Enum implements LocalizedEnum
{
    use StatusTrait;

    #[Description('')]
    const NotSet = null;
    const Unknown = 0;
    const Register = 1;
    const GantiChip = 2;
    const UpdateChip = 3;
    const DeleteChip = 4;
    #[Description('Scan')]
    const Kotor = 10;
    const Gate = 11;
    const Bersih = 20;
    const Grouping = 30;
    const Barcode = 31;
    const DeleteBarcode = 32;
    const Delivery = 40;
    const DeleteDelivery = 41;
    const DeleteTransaksi = 50;
    const Pending = 70;
    const Hilang = 80;
    const Opname = 90;
}
