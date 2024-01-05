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

    public $hide;

    public function mount()
    {
        $this->data_rs = Rs::getOptions()->toArray();
        $this->data_ruangan = Ruangan::getOptions();
        $this->data_jenis = Jenis::getOptions();
    }

    public function render()
    {
        if($this->id_rs){
            $rs_parse = Rs::with(['has_ruangan', 'has_jenis'])->find($this->id_rs);

            $this->data_ruangan = $rs_parse->has_ruangan->pluck(Ruangan::field_name(), Ruangan::field_primary()) ?? [];
            $this->data_jenis = $rs_parse->has_jenis->pluck(Jenis::field_name(), Jenis::field_primary()) ?? [];
        }

        if($rs_id = request()->get('view_rs_id')){
            $this->id_rs = $rs_id;
        }

        if($ruangan_id = request()->get('view_ruangan_id')){
            $this->id_ruangan = $ruangan_id;
        }

        if($jenis_id = request()->get('view_linen_id')){
            $this->id_jenis = $jenis_id;
        }

        // $this->id_jenis = $this->id_ruangan = null;

        return view('livewire.dropdown')->with([
            'id_ruangan' => $this->id_ruangan,
            'id_jenis' => $this->id_jenis,
            'rs_id' => $this->id_rs,
        ]);
    }
}
