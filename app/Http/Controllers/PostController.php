<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;

class PostController extends Controller
{
    public function index(?User $user = null)
    {
        $posts = Post::when($user, function ($query) use ($user) {
            return $query->where('user_id', $user->id);
        })
        ->whereNotNull('image')
        ->whereNotNull('published_at')
        ->orderBy('published_at', 'desc')
        ->paginate(9);

        $authors = User::when($user, function ($query) use ($user) {
            return $query->where('id', $user->id);
        })
        ->whereHas('posts', function($query) {
            return $query->whereNotNull('published_at');
        })
        ->orderBy('user_id')
        ->get();

        return view('posts.index', compact('posts', 'authors'));
    }

    public function show(Post $post)
    {
        if (is_null($post->updated_at)) {
            abort(404);
        }

        if(is_null($post->published_at)) {
            abort(404);
        }

        return view('posts.show', compact('post'));
    }
}
