<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRow;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function sales()
    {

        $rows = DocumentRow::select(
            'products.name',
            DB::raw('SUM(document_rows.kg_estimated) as kg_venduti'),
            DB::raw('SUM(document_rows.total) as fatturato'),
            DB::raw('SUM(document_rows.kg_estimated * products.cost_price) as costo')
        )
        ->join('products','products.id','=','document_rows.product_id')
        ->groupBy('products.name')
        ->get();

        return view('reports.sales', compact('rows'));

    }

}