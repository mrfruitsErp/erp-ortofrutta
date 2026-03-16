<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Client;
use App\Models\DocumentRow;

class RouteController extends Controller
{

public function index()
{

$routes = Route::all();

return view('routes.index',compact('routes'));

}


public function create()
{

return view('routes.create');

}


public function store(Request $request)
{

$route = new Route();

$route->name = $request->name;
$route->day = $request->day;

$route->save();

/* redirect corretto Laravel */

return redirect()->route('routes.index');

}


public function picking($id)
{

$route = Route::findOrFail($id);

$clients = Client::where('route_id',$id)->get();

$rows = DocumentRow::join('documents','documents.id','=','document_rows.document_id')
->join('clients','clients.id','=','documents.client_id')
->join('products','products.id','=','document_rows.product_id')
->where('clients.route_id',$id)
->selectRaw('products.name as product,
SUM(document_rows.boxes) as boxes,
SUM(document_rows.kg_estimated) as kg')
->groupBy('products.name')
->get();

return view('routes.picking',[
'route'=>$route,
'rows'=>$rows,
'clients'=>$clients
]);

}

}