<?php
// app/Models/InnovationVote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InnovationVote extends Model
{
    protected $fillable = ['user_id', 'innovation_id'];

    public function user(): BelongsTo       { return $this->belongsTo(User::class); }
    public function innovation(): BelongsTo { return $this->belongsTo(Innovation::class); }
}
