<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class MovieController extends Controller
{
    //
    public function index(){
        try{
       $movies = Movie::all();
        return response()->json($movies, 200);

         } catch (Exception $e) {
        return response()->json([
            'message' => 'Failed to retrieve movies.',
        ], 500);
    }
    }

    public function store(Request $request){
       try {
            $validated = $request->validate([
                'movie_image'     => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'title_movie'     => 'required|string',
                'screen_type'     => 'required|string',
                'genre'           => 'required|string',
                'duration'        => 'required|string',
                'release'         => 'required|string',
                'classification'  => 'required|string',
            ]);
            if (!$request->hasFile('movie_image')) {
                return response()->json(['message' => 'មិនមានរូបភាពផ្ញើមកទេ។'], 422);
            }
            $imagePath = $request->file('movie_image')->store('movie-img', 'public');
            $validated['movie_image'] = asset('storage/' . $imagePath);
            // Add user_id from authenticated user
            $user = JWTAuth::parseToken()->authenticate();
            $validated['user_id'] = $user->id;
            $movie = Movie::create($validated);
            return response()->json($movie, 200);
        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $ve->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'បញ្ហាកើតឡើងពេលបញ្ចូលទិន្នន័យភាពយន្ត។',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id){
        try{
            $movie = Movie::findOrFail($id);
            return response()->json($movie);
        }catch(Exception $e){
             return response()->json([
            'message' => 'Movie not found',
            ], 404);
        }
    
    }

    public function update(Request $request, $id){
        try{
            $validated = $request->validate([
                'movie_image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'title_movie'     => 'sometimes|string',
                'screen_type'     => 'sometimes|string',
                'genre'           => 'sometimes|string',
                'duration'        => 'sometimes|string',
                'release'         => 'sometimes|string',
                'classification'  => 'sometimes|string',
            ]);

            $movie = Movie::findOrFail($id);

            if ($request->hasFile('movie_image')) {
                // Delete old image if exists
                if ($movie->movie_image) {
                    $oldPath = str_replace(asset('storage') . '/', '', $movie->movie_image);
                    Storage::disk('public')->delete($oldPath);
                }

                $newImagePath = $request->file('movie_image')->store('movie-img', 'public');
                $validated['movie_image'] = asset('storage/' . $newImagePath);
            }

            $movie->update($validated);

            return response()->json($movie, 200);

        } catch (ValidationException $ve) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $ve->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Movie not found'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong while updating the movie.',
                'error'   => $e->getMessage()
            ], 500);

        }catch(Exception){
            
        }
    }

    public function destroy($id){
        try{
            $movie = Movie::findOrFail($id);
            if(!$movie){
                return response()->json(['message' => 'Location not found'], status: 404);
            }

            if ($movie->movie_image) {
            $imagePath = str_replace(asset('storage') . '/', '', $movie->movie_image);
            Storage::disk('public')->delete($imagePath);
            }
            $movie->delete();

            return response()->json([
                'message'=>'Location deleted successfully'
            ],200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message'=>'Location not found'
            ],404);
        }
    }

  public function moviesUser()
    {
            try {
                $user_id = JWTAuth::parseToken()->authenticate()->id;
                $movies = Movie::where('user_id', $user_id)->get();

            if ($movies->isEmpty()) {
                return response()->json([
                    'message' => 'Movie not found'
                ], 404);
            }

            return response()->json([
                'message' => 'User movies retrieved successfully',
                'movies' => $movies,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error retrieving user movies',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}