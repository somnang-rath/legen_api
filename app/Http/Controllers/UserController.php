<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
    public function index(){
        $user=User::all();
        return response()->json($user);
    }

    public function store(Request $request){
       try{
         $validate=$request->validate(
            [
                'name'      =>'required|string',
                'dob'       =>'required|string',
                'address'   =>'required|string',
                'phone'     =>'required|string',
                'email'     =>'required|string',
                'profile'   => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'password'  =>'required|string',
            ]
        );
         if (!$request->hasFile('profile')) {
                return response()->json(['message' => 'មិនមានរូបភាពផ្ញើមកទេ។'], 422);
            }
            $imagePath = $request->file('profile')->store('profile', 'public');
            $validate['profile'] = asset('storage/' . $imagePath);

            $user=User::create($validate);
            return response()->json($user);
       }catch(ValidationException $ve){
            return response()->json([
                    'message'=>$ve->errors()
                ]);
       }
    }
    public function show($id){
        try{
            $user=User::findOrFail($id);
            return response()->json($user,200);
        }catch(Exception $e){
             return response()->json([
            'message' => 'Movie not found',
            ], 404);
        }
    }
}