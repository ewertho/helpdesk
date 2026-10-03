<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/** Resposta pronta: texto que a equipe insere com um clique ao responder. */
#[Fillable(['title', 'body'])]
class CannedReply extends Model {}
