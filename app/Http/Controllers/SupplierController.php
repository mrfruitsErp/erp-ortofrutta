<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{

    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {

        Supplier::create([
            'company_name' => $request->company_name,
            'vat_number' => $request->vat_number,
            'phone' => $request->phone,
            'email' => $request->email,
            'city' => $request->city
        ]);

        return redirect('/suppliers');

    }

}