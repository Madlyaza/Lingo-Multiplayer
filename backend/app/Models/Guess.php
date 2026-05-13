<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guess extends Model
{
    protected $fillable = ['game_id', 'user_id', 'round', 'word', 'feedback', 'is_correct'];

    protected $casts = [
        'feedback'   => 'array',
        'is_correct' => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function game() { return $this->belongsTo(Game::class); }
}
