<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Bus;
use Livewire\Component;
use Livewire\Attributes\Url;

class Notification extends Component
{
    #[Url]
    public $batch = '';
    public $job = 0;
    public $max;
    public $percent;
    public $status = 'proses';

    public function render()
    {
        $bus = Bus::findBatch($this->batch);

        if($bus)
        {
            $this->job = ($bus->totalJobs - $bus->pendingJobs);
            $this->max = $bus->totalJobs;
            $this->percent = intval(($this->job / $bus->totalJobs) * 100);

            if($bus->pendingJobs == 0)
            {
                $this->status = 'selesai';
            }
        }

        return view('livewire.notification');
    }
}
