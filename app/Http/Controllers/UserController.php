<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    //
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
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

            $token = JWTAuth::fromUser($user);

            return response()->json(['user' => $user, 'token' => $token],201);
        }catch(ValidationException $ve){
            return response()->json([
                'message' => $ve->errors()
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => 'User not found',
            ], 404);
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
   public function update(Request $request)
{
    try {
        $userid = JWTAuth::parseToken()->authenticate();
        $user = User::findOrFail($userid->id);
        $validate = $request->validate([
            'name'      => 'sometimes|string',
            'dob'       => 'sometimes|string',
            'address'   => 'sometimes|string',
            'phone'     => 'sometimes|string',
            'profile'   => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // If the password is provided, hash it
        if ($request->hasFile('profile')) {
                // Delete old image if exists
                if ($user->profile) {
                    $oldPath = str_replace(asset('storage') . '/', '', $user->profile);
                    Storage::disk('public')->delete($oldPath);
                }

                $newImagePath = $request->file('profile')->store('profile', 'public');
                $validate['profile'] = asset('storage/' . $newImagePath);
            }
       
        // Update user with validated data
        $user->update($validate);
        // Return success response with updated user data
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
            'user_id' => $userid->id
        ], 200);

    } catch (ValidationException $ve) {
        return response()->json([
            'message' => $ve->errors()
        ], 422); // Validation error status code
    } catch (Exception $e) {
        // General error handler
        return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function destroy(){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            $user = User::findOrFail($user->id);
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