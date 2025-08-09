<?php

namespace App\Http\Controllers;

use App\Http\Resources\HistoryResource;
use App\Models\Hestory;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\History;
class HestoryController extends Controller
{
    //
    public function index(){
        $history=Hestory::all();
        return response()->json($history);
    }

    public function store(Request $request){
         $id = JWTAuth::parseToken()->authenticate()->id;
         $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);
        $validated['user_id'] =$id;
        $existingHistory = Hestory::where('user_id', $id)
                                 ->where('movie_id', $validated['movie_id'])
                                 ->first();

        if ($existingHistory) {
            return response()->json([
                'message' => 'Movie already exists in your history',
                'history' => $existingHistory
            ], 409); // Conflict status code
        }
        $history = Hestory::create($validated);
        return response()->json($history,200);
    }
    public function show(){
        $id = JWTAuth::parseToken()->authenticate()->id;
        $hestories = Hestory::where('user_id', $id)->get();

        return response()->json([
            'data' => HistoryResource::collection($hestories)
        ], 200);
    }
    
   public function destroy($id){
    try{
         $id = JWTAuth::parseToken()->authenticate()->id;
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