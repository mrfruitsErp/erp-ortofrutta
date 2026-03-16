<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentRow;
use App\Models\Stock;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | KPI OGGI
        |--------------------------------------------------------------------------
        */

        $docs_today = Document::whereDate('date', $today)->get();
        $docs_count = $docs_today->count();
        $revenue    = $docs_today->sum('total');

        $cost = DocumentRow::whereHas('document', fn($q) => $q->whereDate('date', $today))
            ->with('product')
            ->get()
            ->sum(fn($row) => ($row->kg_real ?? $row->kg_estimated) * ($row->product->cost_price ?? 0));

        $margin         = $revenue - $cost;
        $margin_percent = $revenue > 0 ? ($margin / $revenue) * 100 : 0;

        /*
        |--------------------------------------------------------------------------
        | CREDITI CLIENTI
        |--------------------------------------------------------------------------
        */

        $crediti       = Document::sum('total');
        $crediti_count = Document::count();

        /*
        |--------------------------------------------------------------------------
        | SOTTO SCORTA
        |--------------------------------------------------------------------------
        */

        $low_stock = Product::whereHas('stock', function ($q) {
            $q->whereColumn('quantity', '<=', 'min_stock');
        })->orWhereDoesntHave('stock')->get();

        /*
        |--------------------------------------------------------------------------
        | GRAFICO VENDITE ULTIMI 6 MESI
        |--------------------------------------------------------------------------
        */

        $monthNames = ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];

        $salesData = Document::selectRaw('MONTH(date) as month, SUM(total) as total')
            ->where('date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $salesData->pluck('month')->map(fn($m) => $monthNames[$m - 1])->toArray();
        $sales  = $salesData->pluck('total')->toArray();

        /*
        |--------------------------------------------------------------------------
        | TOP CLIENTI PER MARGINE (ultimi 30 giorni)
        |--------------------------------------------------------------------------
        */

        $top_clients = DB::table('document_rows as dr')
            ->join('documents as d', 'd.id', '=', 'dr.document_id')
            ->join('clients as c', 'c.id', '=', 'd.client_id')
            ->join('products as p', 'p.id', '=', 'dr.product_id')
            ->where('d.date', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('c.company_name as client,
                SUM(dr.total) as revenue,
                SUM((COALESCE(dr.kg_real, dr.kg_estimated)) * COALESCE(p.cost_price, 0)) as cost_total')
            ->groupBy('c.company_name')
            ->orderByRaw('SUM(dr.total) - SUM((COALESCE(dr.kg_real, dr.kg_estimated)) * COALESCE(p.cost_price, 0)) DESC')
            ->limit(5)
            ->get()
            ->map(fn($row) => (object)[
                'client' => $row->client,
                'margin' => $row->revenue - $row->cost_total,
            ]);

        /*
        |--------------------------------------------------------------------------
        | TOP PRODOTTI PER MARGINE (ultimi 30 giorni)
        |--------------------------------------------------------------------------
        */

        $top_products = DB::table('document_rows as dr')
            ->join('documents as d', 'd.id', '=', 'dr.document_id')
            ->join('products as p', 'p.id', '=', 'dr.product_id')
            ->where('d.date', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('p.name as product,
                SUM(dr.total) as revenue,
                SUM((COALESCE(dr.kg_real, dr.kg_estimated)) * COALESCE(p.cost_price, 0)) as cost_total')
            ->groupBy('p.name')
            ->orderByRaw('SUM(dr.total) - SUM((COALESCE(dr.kg_real, dr.kg_estimated)) * COALESCE(p.cost_price, 0)) DESC')
            ->limit(5)
            ->get()
            ->map(fn($row) => (object)[
                'product' => $row->product,
                'margin'  => $row->revenue - $row->cost_total,
            ]);

        /*
        |--------------------------------------------------------------------------
        | ULTIMI DOCUMENTI
        |--------------------------------------------------------------------------
        */

        $latest_docs = Document::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'revenue',
            'cost',
            'margin',
            'margin_percent',
            'docs_count',
            'crediti',
            'crediti_count',
            'low_stock',
            'months',
            'sales',
            'top_clients',
            'top_products',
            'latest_docs',
        ));
    }
}