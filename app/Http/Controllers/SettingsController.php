<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{

public function orders()
{

$cutoff = Setting::get('order_cutoff_time') ?? '22:00';

return view('settings.orders',compact('cutoff'));

}

public function saveOrders(Request $request)
{

Setting::set('order_cutoff_time',$request->cutoff);

return redirect()->back()->with('success','Impostazioni salvate');

}

}
