<?php

namespace App\Livewire;

use App\Dao\Models\Jenis;
use App\Dao\Models\Rs;
use App\Dao\Models\Ruangan;
use Livewire\Component;

class Dropdown extends Component
{
    public $id_rs;
    public $id_ruangan;
    public $id_jenis;

    public $data_rs;
    public $data_ruangan;
    public $data_jenis;

    public function mount()
    {
        $this->data_rs = Rs::getOptions();
        $this->data_ruangan = Ruangan::getOptions();
        $this->data_jenis = Jenis::getOptions();
    }

    public function render()
    {
        if($this->id_rs){
            $data_rs = Rs::with(['has_ruangan', 'has_jenis'])->find($this->id_rs);

            $this->data_ruangan = $data_rs->has_ruangan->pluck(Ruangan::field_name(), Ruangan::field_primary()) ?? [];
            $this->data_jenis = $data_rs->has_jenis->pluck(Jenis::field_name(), Jenis::field_primary()) ?? [];
        }

        $this->id_jenis = $this->id_ruangan = null;

        return view('livewire.dropdown');
    }
}
