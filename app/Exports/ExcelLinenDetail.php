<?php

namespace App\Exports;

use App\Dao\Models\Detail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExcelLinenDetail implements FromView
{
    use Exportable;

    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function view(): View
    {
        $detail = Detail::all();
        return view('export.'.$this->view)->with([
            'data' => $detail
        ]);
    }
}
