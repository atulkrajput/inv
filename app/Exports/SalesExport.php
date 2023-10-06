<?php

namespace App\Exports;

use App\Model\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromQuery
{
    use Exportable;

    public function __construct($date, $type)
    {
        $this->date = $date;
        $this->type = $type;
    }

    public function query()
    {
        if($this->type == 1):
            return Sale::query()->whereYear('created_at', date('Y', strtotime($this->date)))->whereMonth('created_at', date('m', strtotime($this->date)));
        elseif($this->type == 2):
            return Sale::query()->whereBetween('created_at', [date('Y-m-d', strtotime($this->sdate)), date('Y-m-d', strtotime($this->edate))]);
        endif;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Created at',
            'Updated at'
        ];
    }
}
