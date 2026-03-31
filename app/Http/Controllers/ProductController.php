<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class ProductController extends Controller
{
    private function generateSku(string $category): string
    {
        $prefixes = [
            'Frutta'            => 'FR',
            'Verdura'           => 'VE',
            'Erbe Aromatiche'   => 'ER',
            'Funghi'            => 'FU',
            'Frutta Secca'      => 'FS',
            'Legumi Secchi'     => 'LS',
            'Insalata 4a Gamma' => 'IV',
        ];

        $prefix = $prefixes[$category] ?? 'PR';

        $last = Product::where('sku', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(sku, 3) AS UNSIGNED) DESC')
            ->first();

        $nextNum = 1;
        if ($last && preg_match('/\d+$/', $last->sku, $match)) {
            $nextNum = (int) $match[0] + 1;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        foreach ($products as $product) {
            $product->stock = Stock::where('product_id', $product->id)->first();
        }

        $categories = Product::distinct()->orderBy('category')->pluck('category')->filter();
        $origins    = Product::distinct()->orderBy('origin')->pluck('origin')->filter();

        return view('products.index', compact('products', 'categories', 'origins'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'category'          => 'required|string|max:255',
            'modalita_vendita'  => 'required|in:cassa_kg,cassa_collo,kg_liberi,pezzo,peso_step',
        ]);

        $modalita = $request->modalita_vendita;

        $product = Product::create([
            'name'              => $request->name,
            'origin'            => $request->origin,
            'modalita_vendita'  => $modalita,
            'step_grammi'       => $modalita === 'peso_step' ? ($request->step_grammi ?? 100) : null,
            'avg_box_weight'    => $request->avg_box_weight ?? 0,
            'tara'              => $request->tara ?? 0,
            'pieces_per_box'    => $request->pieces_per_box ?? 0,
            'price'             => $request->price ?? 0,
            'cost_price'        => $request->cost_price ?? 0,
            'price_horeca'      => $request->price_horeca ?? $request->price ?? 0,
            'price_dettaglio'   => $request->price_dettaglio ?? $request->price ?? 0,
            'price_gdo'         => $request->price_gdo ?? $request->price ?? 0,
            'vat_rate'          => $request->vat_rate ?? 4,
            'category'          => $request->category,
            'disponibilita'     => $request->disponibilita ?? 'disponibile',
            'ordine_min'        => $request->ordine_min ?? 1,
            'ordine_min_kg'     => $request->ordine_min_kg,
            'ordine_max'        => $request->ordine_max,
            'sku'               => $this->generateSku($request->category),
        ]);

        Stock::create([
            'product_id' => $product->id,
            'quantity'   => $request->new_stock_qty ?? 0,
            'min_stock'  => $request->min_stock ?? 0,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Prodotto creato con successo');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $stock   = Stock::where('product_id', $id)->first();

        return view('products.edit', compact('product', 'stock'));
    }

    public function update(Request $request, $id)
    {
        $product  = Product::findOrFail($id);
        $modalita = $request->modalita_vendita ?? $product->modalita_vendita;

        $product->update([
            'name'              => $request->name,
            'origin'            => $request->origin,
            'modalita_vendita'  => $modalita,
            'step_grammi'       => $modalita === 'peso_step' ? ($request->step_grammi ?? 100) : null,
            'avg_box_weight'    => $request->avg_box_weight ?? 0,
            'tara'              => $request->tara ?? 0,
            'pieces_per_box'    => $request->pieces_per_box ?? 0,
            'price'             => $request->price ?? 0,
            'cost_price'        => $request->cost_price ?? 0,
            'price_horeca'      => $request->price_horeca ?? $product->price_horeca,
            'price_dettaglio'   => $request->price_dettaglio ?? $product->price_dettaglio,
            'price_gdo'         => $request->price_gdo ?? $product->price_gdo,
            'vat_rate'          => $request->vat_rate ?? 4,
            'disponibilita'     => $request->disponibilita ?? 'disponibile',
            'ordine_min'        => $request->ordine_min ?? 1,
            'ordine_min_kg'     => $request->ordine_min_kg,
            'ordine_max'        => $request->ordine_max,
            'category'          => $request->category,
        ]);

        if ($request->filled('new_stock_qty')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0, 'min_stock' => 0]
            );
            $stock->quantity = $request->new_stock_qty;
            $stock->save();
        }

        if ($request->filled('min_stock')) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id],
                ['quantity' => 0, 'min_stock' => 0]
            );
            $stock->min_stock = $request->min_stock;
            $stock->save();
        }

        return redirect()->back()->with('success', 'Prodotto aggiornato');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        Stock::where('product_id', $id)->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Prodotto eliminato.');
    }

    public function inlineUpdate(Request $request, Product $product)
    {
        $allowed = ['price', 'cost_price', 'price_horeca', 'price_dettaglio', 'price_gdo', 'origin', 'disponibilita'];

        $field = $request->input('field');
        $value = $request->input('value');

        if (!in_array($field, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Campo non consentito'], 403);
        }

        $isPrice = str_starts_with($field, 'price');
        if ($isPrice && (!is_numeric($value) || $value < 0)) {
            return response()->json(['success' => false, 'message' => 'Prezzo non valido'], 422);
        }

        $product->update([$field => $value]);

        return response()->json(['success' => true, 'value' => $product->fresh()->$field]);
    }

    public function massiveUpdate(Request $request)
    {
        $ids    = $request->input('ids', []);
        $action = $request->input('action');
        $value  = $request->input('value');

        if (empty($ids)) {
            return response()->json(['success' => false]);
        }

        try {
            foreach ($ids as $id) {
                $product = Product::find($id);
                if (!$product) continue;

                switch ($action) {
                    case 'disp_set':
                        if (in_array($value, ['disponibile', 'su_richiesta', 'non_disponibile'])) {
                            $product->disponibilita = $value;
                            $product->save();
                        }
                        break;
                    case 'price_set':
                        $product->price = $value;
                        $product->save();
                        break;
                    case 'cost_percent':
                        $product->cost_price *= (1 + ($value / 100));
                        $product->save();
                        break;
                    case 'price_percent':
                        $product->price *= (1 + ($value / 100));
                        $product->save();
                        break;
                    case 'stock_set':
                        $stock = Stock::firstOrCreate(
                            ['product_id' => $product->id],
                            ['quantity' => 0]
                        );
                        $stock->quantity = $value;
                        $stock->save();
                        break;
                    case 'min_stock':
                        $stock = Stock::firstOrCreate(
                            ['product_id' => $product->id],
                            ['quantity' => 0]
                        );
                        $stock->min_stock = $value;
                        $stock->save();
                        break;
                }
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage()
            ], 500);
        }
    }

    // ── EXPORT CSV ────────────────────────────────────────
    public function export(Request $request)
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        foreach ($products as $product) {
            $product->stock = Stock::where('product_id', $product->id)->first();
        }

        if ($request->filled('category')) {
            $products = $products->where('category', $request->category);
        }

        $filename = 'prodotti_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = [
            'SKU',
            'Nome',
            'Categoria',
            'Origine',
            'Modalità Vendita',
            'Disponibilità',
            'Costo (€/kg)',
            'Prezzo Base (€/kg)',
            'Prezzo HoReCa (€/kg)',
            'Prezzo Dettaglio (€/kg)',
            'Prezzo GDO (€/kg)',
            'IVA %',
            'Peso Medio Cassa (kg)',
            'Tara (kg)',
            'Pezzi per Cassa',
            'Stock Attuale (kg)',
            'Scorta Minima (kg)',
            'Ordine Min',
            'Ordine Min Kg',
            'Ordine Max',
        ];

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($products as $p) {
                $dispMap = [
                    'disponibile'     => 'Disponibile',
                    'su_richiesta'    => 'Su richiesta',
                    'non_disponibile' => 'Non disponibile',
                ];
                $modMap = [
                    'cassa_kg'    => 'Cassa (kg)',
                    'cassa_collo' => 'Cassa (collo)',
                    'kg_liberi'   => 'Kg liberi',
                    'pezzo'       => 'Pezzo',
                    'peso_step'   => 'Peso step',
                ];

                fputcsv($file, [
                    $p->sku ?? '',
                    $p->name ?? '',
                    $p->category ?? '',
                    $p->origin ?? '',
                    $modMap[$p->modalita_vendita] ?? $p->modalita_vendita ?? '',
                    $dispMap[$p->disponibilita] ?? $p->disponibilita ?? '',
                    number_format($p->cost_price ?? 0, 2, ',', ''),
                    number_format($p->price ?? 0, 2, ',', ''),
                    number_format($p->price_horeca ?? 0, 2, ',', ''),
                    number_format($p->price_dettaglio ?? 0, 2, ',', ''),
                    number_format($p->price_gdo ?? 0, 2, ',', ''),
                    $p->vat_rate ?? 4,
                    number_format($p->avg_box_weight ?? 0, 3, ',', ''),
                    number_format($p->tara ?? 0, 3, ',', ''),
                    $p->pieces_per_box ?? 0,
                    number_format($p->stock?->quantity ?? 0, 3, ',', ''),
                    number_format($p->stock?->min_stock ?? 0, 3, ',', ''),
                    $p->ordine_min ?? '',
                    $p->ordine_min_kg ?? '',
                    $p->ordine_max ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── IMPORT CSV ────────────────────────────────────────
    // Usa SKU come chiave. Aggiorna prezzi, costo, disponibilità,
    // origine, stock, scorta min, pesi, ordine min/max.
    // NON modifica: Nome, Categoria, Modalità Vendita (sicurezza).
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path   = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return back()->with('error', 'Impossibile leggere il file.');
        }

        // Rimuovi BOM UTF-8 se presente
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        // Leggi intestazione
        $header = fgetcsv($handle, 0, ';');
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'File CSV vuoto o intestazione mancante.');
        }

        $header = array_map(fn($h) => trim(mb_strtolower($h)), $header);

        // Indici colonne
        $col = [
            'sku'             => array_search('sku', $header),
            'origin'          => array_search('origine', $header),
            'disponibilita'   => array_search('disponibilità', $header),
            'cost_price'      => array_search('costo (€/kg)', $header),
            'price'           => array_search('prezzo base (€/kg)', $header),
            'price_horeca'    => array_search('prezzo horeca (€/kg)', $header),
            'price_dettaglio' => array_search('prezzo dettaglio (€/kg)', $header),
            'price_gdo'       => array_search('prezzo gdo (€/kg)', $header),
            'vat_rate'        => array_search('iva %', $header),
            'avg_box_weight'  => array_search('peso medio cassa (kg)', $header),
            'tara'            => array_search('tara (kg)', $header),
            'pieces_per_box'  => array_search('pezzi per cassa', $header),
            'stock_qty'       => array_search('stock attuale (kg)', $header),
            'min_stock'       => array_search('scorta minima (kg)', $header),
            'ordine_min'      => array_search('ordine min', $header),
            'ordine_min_kg'   => array_search('ordine min kg', $header),
            'ordine_max'      => array_search('ordine max', $header),
        ];

        if ($col['sku'] === false) {
            fclose($handle);
            return back()->with('error', 'Colonna SKU non trovata. Usa il file esportato da questo sistema senza rinominare le colonne.');
        }

        $dispMap = [
            'disponibile'     => 'disponibile',
            'su richiesta'    => 'su_richiesta',
            'non disponibile' => 'non_disponibile',
        ];

        // Converti numero formato italiano → float
        $toFloat = fn($v) => (float) str_replace(',', '.', str_replace('.', '', trim($v ?? '0')));

        $updated  = 0;
        $notFound = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $rowNum++;
            if (count($data) < 2) { $skipped++; continue; }

            $sku = trim($data[$col['sku']] ?? '');
            if (empty($sku)) { $skipped++; continue; }

            $product = Product::where('sku', $sku)->first();
            if (!$product) {
                $notFound++;
                $errors[] = "Riga $rowNum: SKU \"$sku\" non trovato.";
                continue;
            }

            try {
                $upd = [];

                // Origine
                if ($col['origin'] !== false && isset($data[$col['origin']])) {
                    $upd['origin'] = trim($data[$col['origin']]);
                }

                // Disponibilità
                if ($col['disponibilita'] !== false && isset($data[$col['disponibilita']])) {
                    $raw = mb_strtolower(trim($data[$col['disponibilita']]));
                    $upd['disponibilita'] = $dispMap[$raw] ?? $product->disponibilita;
                }

                // Prezzi numerici
                foreach (['cost_price', 'price', 'price_horeca', 'price_dettaglio', 'price_gdo'] as $f) {
                    if ($col[$f] !== false && isset($data[$col[$f]]) && $data[$col[$f]] !== '') {
                        $v = $toFloat($data[$col[$f]]);
                        if ($v >= 0) $upd[$f] = $v;
                    }
                }

                // IVA
                if ($col['vat_rate'] !== false && isset($data[$col['vat_rate']]) && $data[$col['vat_rate']] !== '') {
                    $vat = (int) $data[$col['vat_rate']];
                    if (in_array($vat, [0, 4, 5, 10, 22])) $upd['vat_rate'] = $vat;
                }

                // Pesi
                foreach (['avg_box_weight', 'tara'] as $f) {
                    if ($col[$f] !== false && isset($data[$col[$f]]) && $data[$col[$f]] !== '') {
                        $upd[$f] = $toFloat($data[$col[$f]]);
                    }
                }

                // Pezzi per cassa
                if ($col['pieces_per_box'] !== false && isset($data[$col['pieces_per_box']]) && $data[$col['pieces_per_box']] !== '') {
                    $upd['pieces_per_box'] = (int) $data[$col['pieces_per_box']];
                }

                // Ordini
                foreach (['ordine_min', 'ordine_max'] as $f) {
                    if ($col[$f] !== false && isset($data[$col[$f]]) && $data[$col[$f]] !== '') {
                        $upd[$f] = (int) $data[$col[$f]];
                    }
                }
                if ($col['ordine_min_kg'] !== false && isset($data[$col['ordine_min_kg']]) && $data[$col['ordine_min_kg']] !== '') {
                    $upd['ordine_min_kg'] = $toFloat($data[$col['ordine_min_kg']]);
                }

                if (!empty($upd)) $product->update($upd);

                // Stock
                $doStock = false;
                $newQty  = null;
                $newMin  = null;

                if ($col['stock_qty'] !== false && isset($data[$col['stock_qty']]) && $data[$col['stock_qty']] !== '') {
                    $newQty  = $toFloat($data[$col['stock_qty']]);
                    $doStock = true;
                }
                if ($col['min_stock'] !== false && isset($data[$col['min_stock']]) && $data[$col['min_stock']] !== '') {
                    $newMin  = $toFloat($data[$col['min_stock']]);
                    $doStock = true;
                }

                if ($doStock) {
                    $stock = Stock::firstOrCreate(
                        ['product_id' => $product->id],
                        ['quantity' => 0, 'min_stock' => 0]
                    );
                    if ($newQty !== null) $stock->quantity  = $newQty;
                    if ($newMin !== null) $stock->min_stock = $newMin;
                    $stock->save();
                }

                $updated++;

            } catch (\Exception $e) {
                $errors[] = "Riga $rowNum (SKU $sku): " . $e->getMessage();
            }
        }

        fclose($handle);

        $msg  = "✓ $updated prodotti aggiornati";
        if ($notFound > 0) $msg .= " · $notFound SKU non trovati";
        if ($skipped  > 0) $msg .= " · $skipped righe saltate";
        if (!empty($errors)) {
            $msg .= ' · Errori: ' . implode(' | ', array_slice($errors, 0, 3));
            if (count($errors) > 3) $msg .= ' (e altri ' . (count($errors) - 3) . ')';
        }

        $sessionType = empty($errors) && $notFound === 0 ? 'success' : 'warning';
        return redirect()->route('products.index')->with($sessionType, $msg);
    }
}