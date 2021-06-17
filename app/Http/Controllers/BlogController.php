<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use JWTAuth;
use Illuminate\Http\Request;
//use Tymon\JWTAuth\Facades\JWTAuth;

class BlogController extends Controller
{
    //
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();

    }

    public function index()
    {
        return $this->user
            ->blogs()
            ->get(['title', 'details'])
            ->toArray();
    }

    public function show($id)
    {
        $blog = $this->user->blogs()->find($id);

        if (!$blog){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, blog with id' . $id . 'cannot be found'
            ], 400);
        }

        return $blog;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'details' => 'required',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->details = $request->details;

        if ($this->user->blogs()->save($blog))
        {
            return response()->json([
                'success' => true,
                'blog' => $blog
            ]);
        }
        else
        {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, blog could not be added'
            ], 500);
        }



    }

    public function update(Request $request, $id)
    {
        $blog = $this->user->blogs()->find($id);

        if (!$blog){
            return response()->json([
                'success' => false,
                'message' => 'Sorry, blog with id' . $id . 'cannot be found'
            ], 400);
        }

        $updated = $blog->fill($request->all())->save();

        if ($updated){
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, blog could not be updated'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $blog = $this->user->blogs()->find($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, blog with id' . $id . 'cannot be found'
            ], 400);
        }

        if ($blog->delete()){
            return response()->json([
                'success' => true
            ]);
        }

        else{
            return response()->json([
                'success' => false,
                'message' => 'Blog could not be deleted'
            ], 500);
        }
    }
}
