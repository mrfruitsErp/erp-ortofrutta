<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Document;
use Carbon\Carbon;

class PaymentController extends Controller
{

    public function index()
    {

        $payments = Payment::with(['document.client'])->latest()->get();

        return view('payments.index', compact('payments'));

    }


    public function store(Request $request)
    {

        Payment::create([
            'document_id' => $request->document_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'method' => $request->method
        ]);

        return redirect()->back();

    }


    public function crediti()
    {

        $documents = Document::with('client')->get();

        $rows = [];

        foreach($documents as $doc){

            $pagato = Payment::where('document_id',$doc->id)->sum('amount');

            $residuo = $doc->total - $pagato;

            if($residuo > 0){

                // FIX GIORNI
                $giorni = Carbon::parse($doc->date)
                    ->startOfDay()
                    ->diffInDays(now()->startOfDay());

                $rows[] = [
                    'cliente'=>$doc->client->company_name ?? '',
                    'documento'=>$doc->number,
                    'data'=>$doc->date,
                    'importo'=>$residuo,
                    'giorni'=>$giorni
                ];

            }

        }

        return view('payments.crediti',['rows'=>$rows]);

    }

}