<?php

namespace App\Http\Controllers\OneGaleonApp;

use App\Http\Controllers\Controller;
use App\Models\Movimiento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovimientoController extends Controller
{



    public function index(Request $req){
        $inicioMes = $req->input('desde') ?? Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $finMes = $req->input('hasta') ?? Carbon::now()->endOfDay()->format('Y-m-d');
        $user = $req->user();

        $results = Movimiento::where('user_id',$user->id)->whereBetween('created_at',[$inicioMes,$finMes]);
        $ingresos = $results->where('tipo',1)->sum('valor');
        $egresos = $results->where('tipo',0)->sum('valor');
        return response()->json([
            'success'=>true,
            'results'=>[
                'movimientos'=>$results->get(),
                'ingresos' =>$ingresos,
                'egresos' =>$egresos
            ]
        ]);
    }




    public function show(Request $req, $id)
    {
        $user = $req->user();
        $movimiento = Movimiento::where('user_id', $user->id)->find($id);

        if (!$movimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Movimiento no encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'results' => $movimiento
        ]);
    }





    public function store(Request $req){
        $user = $req->user();
        $validator = Validator::make($req->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'valor' => 'required|numeric',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }
        $movimiento = Movimiento::create([
            'user_id'=>$user->id,
            'category_id'=>$req->category_id, // nullable
            'valor'=> $req->valor, // numerico
            'descripcion'=> $req->descripcion, // texto string
            'tipo'=> $req->tipo, // enum 0 o 1
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'created',
            'results'=>$movimiento
        ],201);
    }




    public function update(Request $req, $id)
    {
        $validator = Validator::make($req->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'valor' => 'required|numeric',
            'descripcion' => 'nullable|string|max:255',
            'tipo' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $user = $req->user();
        $movimiento = Movimiento::where('user_id', $user->id)->find($id);

        if (!$movimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Movimiento no encontrado.',
            ], 404);
        }

        $movimiento->update([
            'category_id' => $req->category_id,
            'valor' => $req->valor,
            'descripcion' => $req->descripcion,
            'tipo' => $req->tipo,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Movimiento actualizado.',
            'results' => $movimiento
        ]);
    }





    public function destroy(Request $req, $id)
    {
        $user = $req->user();
        $movimiento = Movimiento::where('user_id', $user->id)->find($id);

        if (!$movimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Movimiento no encontrado.',
            ], 404);
        }

        $movimiento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Movimiento eliminado.'
        ]);
    }

}
