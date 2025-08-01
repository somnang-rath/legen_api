<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Locale;

class LocationController extends Controller
{
    //
    public function index(){
        $location= Location::all();
        return response()->json($location);
    }

    public function store(Request $request){
        $validate = $request->validate([
            'name' => 'required|string',
            'location' => 'required|string',
            'img' => 'nullable|string'
        ]);
        $location=Location::create($validate);
        return response()->json($location,201);
    }

    public function show($id){
        $location= Location::findOrFail($id);
        return response()->json($location);
    }

    public function update(Request $request,$id){
        $validet = $request->validate([
              'name' => 'required|string',
            'location' => 'required|string',
            'img' => 'nullable|string'
        ]);
        $location = Location::findOrFail($id);
        $location->update($validet);
        return response()->json($location);
    }

    public function destroy($id){
        $location = Location::findOrFail($id);
        $location->delete();
        return response()->json([
            'message'=>'Location deleted'
        ],200);
    }
}