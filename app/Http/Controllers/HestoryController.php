<?php

namespace App\Http\Controllers;

use App\Http\Resources\HistoryResource;
use App\Models\Hestory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class HestoryController extends Controller
{
    //
    public function index(){
        $history=Hestory::all();
        return response()->json($history);
    }

    public function store(Request $request){
         $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $history = Hestory::create($validated);
        return response()->json($history,200);
    }
    public function show($id){
        $hestories = Hestory::where('user_id', $id)->get();

        return response()->json([
            'data' => HistoryResource::collection($hestories)
        ], 200);
    }
    
   public function destroy($id){
    try{
        $history=Hestory::findOrFail($id);
        if(!$history){
           return response()->json([
           'message'=>'Data not found'
        ],201);
        }
        $history->delete();
        return response()->json([
            'message'=>'Location deleted successfully'
        ],200);
    }catch(Exception){
        return response()->json([
           'message'=>'Data not found'
        ],404);
    }
   }
}