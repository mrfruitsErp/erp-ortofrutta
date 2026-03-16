<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product', 'document'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totaleUscite  = $movements->where('type', 'OUT')->sum('qty');
        $totaleCarichi = $movements->where('type', 'IN')->sum('qty');
        $totaleOggi    = $movements->where('movement_date', today()->toDateString())->count();

        return view('movements.index', compact(
            'movements',
            'totaleUscite',
            'totaleCarichi',
            'totaleOggi'
        ));
    }
}