<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Website;
use App\Notifications\NewPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::paginate(
            $request->input('per_page', 10)
        );
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'url' => 'required|url',
            'website_id' => 'required|exists:websites,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validated = $validator->validated();
        $validated = $validator->safe()->only(['title', 'description', 'url', 'website_id']);

        $post =  Website::find($validated['website_id'])->posts()->create($validated);

        // NOTIFACTION SYSTEM
        // Improvment : maybe put this code in observer class
        // so we can notify subscriber wherever post is created

        $subscribers = $post->website->subscribersCached;

        Notification::send($subscribers, new NewPost($post));

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        return response()->json(Post::findOrFail($id), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'url' => 'required|url',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $validated = $validator->safe()->only(['title', 'description', 'url']);
        $post = Post::findOrFail($id);
        $post->update($validated);
        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(null, 204);
    }
}
