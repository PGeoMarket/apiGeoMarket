<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::query()
            ->when($request->user_id, fn($q) => $q->byUser($request->user_id))
            ->when($request->publication_id, fn($q) => $q->byPublication($request->publication_id))
            ->when($request->valor_estrella, fn($q) => $q->byStars($request->valor_estrella))
            ->when($request->search, fn($q) => $q->search($request->search))
            ->with(['user', 'publication'])
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'texto'           => 'required|string',
            'valor_estrella'  => 'nullable|integer|min:1|max:5',
            'user_id'         => 'required|exists:users,id',
            'publication_id'  => 'required|exists:publications,id',
        ]);

        $comment = Comment::create($validated);

        return response()->json($comment, 201);
    }

    public function show($id)
    {
        $comment = Comment::with(['user', 'publication'])->findOrFail($id);
        return response()->json($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'texto'          => 'string',
            'valor_estrella' => 'nullable|integer|min:1|max:5',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

    if (!$comment) {
        return response()->json([
            'error' => 'comentario no encontrada.'
        ], 404);
    }

    if ($comment->delete()) {
        return response()->json([
            'message' => 'comentario eliminada correctamente.'
        ], 200);
    }

    return response()->json([
        'error' => 'No se pudo eliminar el comentario.'
    ], 400);
    }
    
}
