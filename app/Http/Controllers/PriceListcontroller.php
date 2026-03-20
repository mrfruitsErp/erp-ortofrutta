<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\PaymentMethod;

class PriceListController extends Controller
{
    public function index()
    {
        $priceLists = PriceList::withCount('clients', 'items')
            ->orderBy('ordine')
            ->get();

        return view('price-lists.index', compact('priceLists'));
    }

    public function edit($id)
    {
        $priceList      = PriceList::findOrFail($id);
        $paymentMethods = PaymentMethod::attivi()->get();

        // Tutti i prodotti con eventuale item del listino
        $products = Product::orderBy('category')->orderBy('name')->get();

        $items = PriceListItem::where('price_list_id', $id)
            ->get()
            ->keyBy('product_id');

        return view('price-lists.edit', compact('priceList', 'paymentMethods', 'products', 'items'));
    }

    public function update(Request $request, $id)
    {
        $priceList = PriceList::findOrFail($id);

        $priceList->update([
            'nome'               => $request->nome,
            'descrizione'        => $request->descrizione,
            'sconto_default_pct' => $request->sconto_default_pct ?? 0,
            'puo_ordinare_kg'    => $request->has('puo_ordinare_kg'),
            'ordine_min_importo' => $request->ordine_min_importo ?? 0,
            'payment_method_id'  => $request->payment_method_id,
        ]);

        // Salva prezzi per prodotto
        $productIds = $request->input('prod_id', []);

        foreach ($productIds as $index => $productId) {
            if (!$productId) continue;

            $prezzoOverride = $request->input("prezzo_override.$index");
            $scontoPct      = $request->input("sconto_pct.$index");
            $minQty         = $request->input("min_qty.$index");
            $maxQty         = $request->input("max_qty.$index");
            $minQtyKg       = $request->input("min_qty_kg.$index");
            $bloccato       = $request->has("bloccato.$index");

            // Se tutto è vuoto/default, elimina la riga
            $hasData = $prezzoOverride || $scontoPct || $minQty || $maxQty || $minQtyKg || $bloccato;

            if ($hasData) {
                PriceListItem::updateOrCreate(
                    ['price_list_id' => $id, 'product_id' => $productId],
                    [
                        'prezzo_override' => $prezzoOverride ?: null,
                        'sconto_pct'      => $scontoPct ?: null,
                        'min_qty'         => $minQty ?: null,
                        'max_qty'         => $maxQty ?: null,
                        'min_qty_kg'      => $minQtyKg ?: null,
                        'bloccato'        => $bloccato,
                    ]
                );
            } else {
                // Rimuovi override se tutto è vuoto
                PriceListItem::where('price_list_id', $id)
                    ->where('product_id', $productId)
                    ->delete();
            }
        }

        return redirect()->route('price-lists.edit', $id)
            ->with('success', 'Listino "' . $priceList->nome . '" aggiornato.');
    }
}