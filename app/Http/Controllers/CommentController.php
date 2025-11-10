<?php

namespace App\Http\Controllers;


use Illuminate\Routing\Controller; 
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 

class CommentController extends Controller
{
    public function __construct()
    {
        
        $this->middleware('auth');
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:1000'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ]);

        $comment = $post->comments()->create([
            
            'user_id' => Auth::user()->id, 
            'parent_id' => $request->parent_id,
            'body' => $request->body,
            'is_approved' => true, 
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);
        
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }
}
