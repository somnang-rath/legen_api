<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;

class OfferController extends Controller
{
    //
    public function index()
    {
        $offers = Offer::all();
        return response()->json($offers, 200);
    }

    public function store(Request $request)
    {
        try {
            $validate =$request->validate([
                'img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'title' => 'required|string|max:255',
                'date_time' => 'required|string',
                'description' => 'nullable|string',
            ]);
            if (!$request->hasFile('img')) {
                return response()->json(['message' => 'Image file is required.'], 422);
            }
            $imagePath = $request->file('img')->store('offer-img', 'public');
            $validate['img'] = asset('storage/' . $imagePath);
            // Create the offer using the validated data
            $offer = Offer::create($validate);
        return response()->json($offer, 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
       
    }
    public function show($id)
    {
        try {
            $offer = Offer::findOrFail($id);
            return response()->json($offer, 200);
        }catch (\Exception $e) {
            return response()->json(['error' => 'Offer not found'], 404);
        }
   
    }

 public function update(Request $request, $id)
{
    try {
        $validate = $request->validate([
            'img' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'sometimes|string|max:255',
            'date_time' => 'sometimes|string',
            'description' => 'sometimes|string',
        ]);
         $offer = Offer::findOrFail($id);
         if ($request->hasFile('img')) {
                // Delete old image if exists
                if ($offer->img) {
                    $oldPath = str_replace(asset('storage') . '/', '', $offer->img);
                    Storage::disk('public')->delete($oldPath);
                }

                $newImagePath = $request->file('img')->store('offer-img', 'public');
                $validated['img'] = asset('storage/' . $newImagePath);
            }
        $offer->update($validate);

        return response()->json($validate, 200);
    } catch (ValidationException $e) {
        return response()->json(['error' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function destroy($id)
    {
        try {
            $offer = Offer::findOrFail($id);
            if ($offer->img) {
                $imagePath = str_replace(asset('storage') . '/', '', $offer->img);
                Storage::disk('public')->delete($imagePath);
            }
            $offer->delete();
            return response()->json(['message' => 'Offer deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Offer not found'], 404);
        }
    }
    
}