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


    public function prodotti()
    {
        $rows = DocumentRow::select(
            'products.name',
            DB::raw('SUM(document_rows.kg_estimated) as kg_venduti'),
            DB::raw('SUM(document_rows.total) as fatturato'),
            DB::raw('SUM(document_rows.kg_estimated * products.cost_price) as costo')
        )
        ->join('products','products.id','=','document_rows.product_id')
        ->groupBy('products.name')
        ->orderByDesc('fatturato')
        ->get();

        // 👉 calcoli margini
        $rows = $rows->map(function($p){
            $margin = $p->fatturato - $p->costo;
            $percent = $p->fatturato > 0 ? ($margin / $p->fatturato) * 100 : 0;

            $p->margin = $margin;
            $p->percent = $percent;

            return $p;
        });

        return view('reports.prodotti', ['products' => $rows]);
    }


    public function clienti()
    {
        $rows = DocumentRow::select(
            'clients.company_name as name',
            DB::raw('SUM(document_rows.total) as fatturato'),
            DB::raw('SUM(document_rows.kg_estimated * products.cost_price) as costo')
        )
        ->join('documents','documents.id','=','document_rows.document_id')
        ->join('clients','clients.id','=','documents.client_id')
        ->join('products','products.id','=','document_rows.product_id')
        ->groupBy('clients.company_name')
        ->orderByDesc('fatturato')
        ->get();

        // 👉 calcoli margini
        $rows = $rows->map(function($c){
            $margin = $c->fatturato - $c->costo;
            $percent = $c->fatturato > 0 ? ($margin / $c->fatturato) * 100 : 0;

            $c->margin = $margin;
            $c->percent = $percent;

            return $c;
        });

        return view('reports.clienti', ['clients' => $rows]);
    }

}