<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Uma linha do histórico: resposta, nota interna ou evento (mudança de status, atribuição). */
#[Fillable(['user_id', 'body', 'internal', 'event'])]
class Comment extends Model
{
    protected function casts(): array
    {
        return ['internal' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
