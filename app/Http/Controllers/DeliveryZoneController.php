<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryZone;

class DeliveryZoneController extends Controller
{

public function index()
{

$zones = DeliveryZone::orderBy('name')->get();

return view('settings.delivery_zones',compact('zones'));

}

public function store(Request $request)
{

DeliveryZone::create([
'name' => $request->name,
'cap_start' => $request->cap_start,
'cap_end' => $request->cap_end,
'active' => $request->active ? 1 : 0
]);

return redirect()->back()->with('success','Zona creata');

}

public function update(Request $request,$id)
{

$zone = DeliveryZone::findOrFail($id);

$zone->name = $request->name;
$zone->cap_start = $request->cap_start;
$zone->cap_end = $request->cap_end;
$zone->active = $request->active ? 1 : 0;

$zone->save();

return redirect()->back()->with('success','Zona aggiornata');

}

public function destroy($id)
{

$zone = DeliveryZone::findOrFail($id);

$zone->delete();

return redirect()->back()->with('success','Zona eliminata');

}

}
