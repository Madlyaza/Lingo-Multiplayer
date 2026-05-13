<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /**
     * List all public waiting rooms.
     */
    public function index()
    {
        $rooms = Room::with('host:id,name')
            ->where('is_public', true)
            ->where('status', 'waiting')
            ->latest()
            ->get();

        return response()->json($rooms);
    }

    /**
     * Create a new room.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:60'],
            'is_public' => ['required', 'boolean'],
        ]);

        $room = Room::create([
            'name'      => $request->name,
            'host_id'   => $request->user()->id,
            'is_public' => $request->is_public,
            'join_code' => $request->is_public ? null : $this->generateJoinCode(),
            'status'    => 'waiting',
        ]);

        $room->load('host:id,name');

        return response()->json($room, 201);
    }

    /**
     * Show a single room.
     */
    public function show(Room $room)
    {
        $room->load('host:id,name');

        return response()->json($room);
    }

    /**
     * Join a private room by join code.
     */
    public function join(Request $request)
    {
        $request->validate([
            'join_code' => ['required', 'string'],
        ]);

        $room = Room::with('host:id,name')
            ->where('join_code', $request->join_code)
            ->where('status', 'waiting')
            ->first();

        if (! $room) {
            return response()->json(['message' => 'Room not found or no longer available.'], 404);
        }

        return response()->json($room);
    }

    /**
     * Generate a human-readable join code like "brave-tiger-42".
     */
    private function generateJoinCode(): string
    {
        $adjectives = ['brave', 'swift', 'bright', 'calm', 'bold', 'lucky', 'happy', 'wild', 'cool', 'smart',
                       'eager', 'fancy', 'jolly', 'kind', 'lively', 'mighty', 'noble', 'proud', 'quiet', 'rapid'];
        $nouns      = ['tiger', 'eagle', 'river', 'storm', 'flame', 'ocean', 'comet', 'forest', 'thunder', 'rocket',
                       'falcon', 'knight', 'panda', 'wolf', 'shark', 'phoenix', 'cobra', 'viper', 'lynx', 'bear'];

        do {
            $code = $adjectives[array_rand($adjectives)]
                  . '-' . $nouns[array_rand($nouns)]
                  . '-' . rand(10, 99);
        } while (Room::where('join_code', $code)->exists());

        return $code;
    }
}
