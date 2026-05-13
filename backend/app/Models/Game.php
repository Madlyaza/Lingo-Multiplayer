<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    const WINNING_SCORE  = 5;
    const MAX_GUESSES    = 10; // 5 per player

    protected $fillable = [
        'room_id', 'current_word', 'round',
        'player1_id', 'player2_id',
        'player1_score', 'player2_score',
        'current_turn_user_id', 'round_first_user_id',
        'status', 'winner_id',
    ];

    public function room()       { return $this->belongsTo(Room::class); }
    public function player1()    { return $this->belongsTo(User::class, 'player1_id'); }
    public function player2()    { return $this->belongsTo(User::class, 'player2_id'); }
    public function winner()     { return $this->belongsTo(User::class, 'winner_id'); }
    public function guesses()    { return $this->hasMany(Guess::class); }

    public function getWordHintAttribute(): string
    {
        return $this->current_word[0] . str_repeat('_', strlen($this->current_word) - 1);
    }

    public function opponentOf(int $userId): int
    {
        return $userId === $this->player1_id ? $this->player2_id : $this->player1_id;
    }
}
