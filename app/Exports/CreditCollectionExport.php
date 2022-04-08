<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class CreditCollectionExport implements FromView, WithTitle
{
    protected $collections;
    protected $request;

    public function __construct($collections,$request)
    {
        $this->collections = $collections;
        $this->request = $request;
    }

    public function view(): View
    {

        return view('exports.collection_report', [
            'collections' => $this->collections,
            'request' => $this->request,
        ]);
    }

    public function title(): string
    {
        return 'Collection Report';
    }
}
