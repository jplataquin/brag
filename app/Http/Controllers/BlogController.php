<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index()
    {
        $posts = Post::where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('blog.index', compact('posts'));
    }

    /**
     * Display the specified blog post.
     */
    public function show(Post $post)
    {
        if (!$post->is_published) {
            abort(404);
        }

        return view('blog.show', compact('post'));
    }
}
