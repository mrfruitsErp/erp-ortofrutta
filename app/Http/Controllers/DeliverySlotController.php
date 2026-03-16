<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliverySlot;

class DeliverySlotController extends Controller
{

public function index()
{

$slots = DeliverySlot::orderBy('start_time')->get();

return view('settings.delivery_slots',compact('slots'));

}

public function store(Request $request)
{

DeliverySlot::create([
'name' => $request->name,
'start_time' => $request->start_time,
'end_time' => $request->end_time,
'active' => $request->active ? 1 : 0
]);

return redirect()->back()->with('success','Slot creato');

}

public function update(Request $request,$id)
{

$slot = DeliverySlot::findOrFail($id);

$slot->name = $request->name;
$slot->start_time = $request->start_time;
$slot->end_time = $request->end_time;
$slot->active = $request->active ? 1 : 0;

$slot->save();

return redirect()->back()->with('success','Slot aggiornato');

}

public function destroy($id)
{

$slot = DeliverySlot::findOrFail($id);

$slot->delete();

return redirect()->back()->with('success','Slot eliminato');

}

}
