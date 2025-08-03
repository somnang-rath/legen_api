<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        $location=$request->all();
        $imagepath=null;
        if($request->hasFile('img')){
            $imagepath=$request->file('img')->store('images','public');
            $location['img']=asset('storage/'.$imagepath);
        }
        $location=Location::create($location);
        return response()->json($location, 201);
     }

    public function show($id){
        $location= Location::findOrFail($id);
        return response()->json($location);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'location' => 'required|string',
                'img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            $location = Location::findOrFail($id);

            $location->name = $validated['name'];
            $location->location = $validated['location'];

            if ($request->hasFile('img')) {
                // Delete old image if exists
                if ($location->img) {
                    $oldPath = str_replace(asset('storage') . '/', '', $location->img);
                    Storage::disk('public')->delete($oldPath);
                }

                $newImagePath = $request->file('img')->store('images', 'public');
                $location->img = asset('storage/' . $newImagePath);
            }

            $location->save();

            return response()->json($location);

        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $ve->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id){
        try{
            $location = Location::findOrFail($id);
            if(!$location){
                return response()->json(['message' => 'Location not found'], status: 404);
            }

            if ($location->img) {
            $imagePath = str_replace(asset('storage') . '/', '', $location->img);
            Storage::disk('public')->delete($imagePath);
            }
            $location->delete();

            return response()->json([
                'message'=>'Location deleted successfully'
            ],200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message'=>'Location not found'
            ],404);
        }
    }
}