<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host_id',
        'guest_id',
        'is_public',
        'join_code',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function host()  { return $this->belongsTo(User::class, 'host_id'); }
    public function guest() { return $this->belongsTo(User::class, 'guest_id'); }
    public function games() { return $this->hasMany(Game::class); }
}
