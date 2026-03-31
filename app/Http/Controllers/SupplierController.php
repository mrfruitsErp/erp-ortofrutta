<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Purchase;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();

        // Calcola totale acquistato per ogni fornitore
        foreach ($suppliers as $supplier) {
            $supplier->totale_acquistato = Purchase::where('supplier_id', $supplier->id)->sum('total');
        }

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'nullable|email',
        ]);

        Supplier::create([
            'company_name' => $request->company_name,
            'vat_number'   => $request->vat_number,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'city'         => $request->city,
            'address'      => $request->address,
            'note'         => $request->note,
        ]);

        return redirect('/suppliers')->with('success', 'Fornitore creato.');
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);

        $purchases = Purchase::with('product')
            ->where('supplier_id', $id)
            ->orderBy('date', 'desc')
            ->get();

        $totale_acquistato = $purchases->sum('total');

        return view('suppliers.show', compact('supplier', 'purchases', 'totale_acquistato'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'nullable|email',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'company_name' => $request->company_name,
            'vat_number'   => $request->vat_number,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'city'         => $request->city,
            'address'      => $request->address,
            'note'         => $request->note,
        ]);

        return redirect()->route('suppliers.show', $id)->with('success', 'Fornitore aggiornato.');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return redirect('/suppliers')->with('success', 'Fornitore eliminato.');
    }
}