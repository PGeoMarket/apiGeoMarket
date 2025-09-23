<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Publication;
use App\Models\Seller;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $this->updateRatings($comment->publication_id);
    }

    public function updated(Comment $comment): void
    {
        $this->updateRatings($comment->publication_id);
    }

    public function deleted(Comment $comment): void
    {
        $this->updateRatings($comment->publication_id);
    }

    private function updateRatings($publicationId): void
    {
        if (!$publicationId) return;

        // Actualizar promedio de la publicación (redondeado a entero)
        $avgPublication = Comment::where('publication_id', $publicationId)
            ->whereNotNull('valor_estrella')
            ->avg('valor_estrella') ?? 0;

        $publication = Publication::find($publicationId);
        $publication->update(['puntuacion_promedio' => round($avgPublication)]);

        // Actualizar promedio del seller (redondeado a entero)
        $avgSeller = Publication::where('seller_id', $publication->seller_id)
            ->where('puntuacion_promedio', '>', 0)
            ->avg('puntuacion_promedio') ?? 0;

        Seller::where('id', $publication->seller_id)
            ->update(['puntuacion_promedio' => round($avgSeller)]);
    }
}