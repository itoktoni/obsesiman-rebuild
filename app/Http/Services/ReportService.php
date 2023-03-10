<?php

namespace App\Http\Services;

use Maatwebsite\Excel\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use App\Plugins\Views;

class ReportService
{
    public $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function generate($repository, $name)
    {
        if (request()->get('action') == 'excel') {
            $filename = $name . '_' . date('Y_m_d') . '.xlsx';
            $data = $repository->generate($name);
            return $this->excel->download($data, $filename);
        }
        if (request()->get('action') == 'preview') {

            $data = request()->except([
                '_token',
                'action'
            ]);
            $data['name'] = $name;

            return redirect()->route(str_replace('_export', '', Route::getCurrentRoute()->getName()), $data);
        } else if (request()->get('action') == 'pdf') {
            $data['preview'] = $repository->data();
            $pdf = PDF::loadView(Views::pdf(config('page'), config('folder'), $name), $data)
                ->setPaper('A4', 'Landscape');
            return $pdf->stream(); // return $pdf->stream();
        }
    }
}
