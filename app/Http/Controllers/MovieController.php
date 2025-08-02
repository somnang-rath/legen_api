<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    //
    public function index(){
        $movies = Movie::all();
        return response()->json($movies);
    }

    public function store(Request $request){
        $validate = $request->validate([
            'movie_image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'title_movie'=>'required|string',
            'screen_type'=>'required|string',
            'duration'=>'required|string',
            'release'=>'required|string',
            'classification'=>'required|string',
        ]);
        $movies=$request->all();
         $imagepath=null;
        if($request->hasFile('movie_image')){
            $imagepath=$request->file('img')->store('movie-img','public');
            $movies['movie_image']=asset('storage/'.$imagepath);
        }
        $movies = Movie::create($movies);
        return response()->json($movies,200);

    }
}