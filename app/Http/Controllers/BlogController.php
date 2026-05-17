<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $posts = BlogPost::published()
            ->when($category, fn($q) => $q->where('category', $category))
            ->latest('published_at')
            ->paginate(12);

        return view('blog.index', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->firstOrFail();

        return view('blog.show', [
            'post' => $post,
        ]);
    }

    public function webhook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'excerpt' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'category' => 'nullable|string|max:50',
            'source_name' => 'nullable|string|max:255',
            'source_url' => 'nullable|url|max:1000',
        ]);

        $title = $request->input('title');
        $slug = Str::slug($title);

        if (BlogPost::where('slug', $slug)->exists()) {
            $slug .= '-' . now()->format('md');
        }

        $post = BlogPost::create([
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $request->input('excerpt'),
            'content' => $request->input('content'),
            'category' => $request->input('category', 'news'),
            'source_name' => $request->input('source_name'),
            'source_url' => $request->input('source_url'),
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Blog post created.',
            'slug' => $post->slug,
        ], 201);
    }
}
