<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    //
    public function index(){
        $user=User::all();
        return response()->json($user);
    }

    public function store(Request $request){
        try{
            $validate = $request->validate([
                'name'      => 'required|string',
                'dob'       => 'required|string',
                'address'   => 'required|string',
                'phone'     => 'required|string',
                'email'     => 'required|string',
                'profile'   => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'password'  => 'required|string',
            ]);
            if (!$request->hasFile('profile')) {
                return response()->json(['message' => 'មិនមានរូបភាពផ្ញើមកទេ។'], 422);
            }
            $imagePath = $request->file('profile')->store('profile', 'public');
            $validate['profile'] = asset('storage/' . $imagePath);
            // Hash the password before saving
            $validate['password'] = bcrypt($validate['password']);
            $user = User::create($validate);
            return response()->json($user);
        }catch(ValidationException $ve){
            return response()->json([
                'message' => $ve->errors()
            ]);
        }
    }
    public function show($id){
        try{
            $user = User::findOrFail($id);
            return response()->json($user, 200);
        }catch(Exception $e){
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }
    public function update(Request $request, $id){
        try{
            $user = User::findOrFail($id);
            $validate = $request->validate([
                'name'      => 'required|string',
                'dob'       => 'required|string',
                'address'   => 'required|string',
                'phone'     => 'required|string',
                'email'     => 'required|string',
                'profile'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'password'  => 'required|string',
            ]);
            if ($request->hasFile('profile')) {
                $imagePath = $request->file('profile')->store('profile', 'public');
                $validate['profile'] = asset('storage/' . $imagePath);
            }
            // Hash the password before updating
            $validate['password'] = bcrypt($validate['password']);
            $user->update($validate);
            return response()->json($user, 200);
        }catch(ValidationException $ve){
            return response()->json([
                'message' => $ve->errors()
            ]);
        }
    }
    public function destroy($id){
        try{
            $user=User::findOrFail($id);
            if ($user->profile) {
                Storage::disk('public')->delete(str_replace(asset('storage/'), '', $user->profile));
            }
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully'
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }

    public function login(Request $request)
    {
       $credentials = $request->only('email', 'password');

    if ( !$token =JWTAuth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

     $user = JWTAuth::user();

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'user' => $user,
    ], 200);
    }
}