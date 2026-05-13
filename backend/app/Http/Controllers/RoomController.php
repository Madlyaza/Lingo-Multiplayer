<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('host:id,name')
            ->where('is_public', true)
            ->where('status', 'waiting')
            ->latest()
            ->get();

        return response()->json($rooms);
    }

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
        return response()->json($this->formatRoom($room), 201);
    }

    public function show(Room $room)
    {
        $room->load(['host:id,name', 'guest:id,name']);
        return response()->json($this->formatRoom($room));
    }

    public function joinRoom(Request $request, Room $room)
    {
        $userId = $request->user()->id;

        if ($userId === $room->host_id) {
            $room->load(['host:id,name', 'guest:id,name']);
            return response()->json($this->formatRoom($room));
        }

        if ($room->guest_id && $room->guest_id !== $userId) {
            return response()->json(['message' => 'Room is full.'], 422);
        }

        if (! $room->guest_id) {
            $room->update(['guest_id' => $userId]);
        }

        $room->load(['host:id,name', 'guest:id,name']);
        return response()->json($this->formatRoom($room));
    }

    public function join(Request $request)
    {
        $request->validate(['join_code' => ['required', 'string']]);

        $room = Room::with(['host:id,name', 'guest:id,name'])
            ->where('join_code', $request->join_code)
            ->where('status', 'waiting')
            ->first();

        if (! $room) {
            return response()->json(['message' => 'Room not found or no longer available.'], 404);
        }

        $userId = $request->user()->id;

        if ($userId !== $room->host_id) {
            if ($room->guest_id && $room->guest_id !== $userId) {
                return response()->json(['message' => 'Room is full.'], 422);
            }
            if (! $room->guest_id) {
                $room->update(['guest_id' => $userId]);
                $room->refresh()->load(['host:id,name', 'guest:id,name']);
            }
        }

        return response()->json($this->formatRoom($room));
    }

    private function formatRoom(Room $room): array
    {
        $data = $room->toArray();
        $activeGame = $room->games()->where('status', 'in_progress')->latest()->first();
        $data['current_game_id'] = $activeGame?->id;
        return $data;
    }

    private function generateJoinCode(): string
    {
        $adjectives = ['brave','swift','bright','calm','bold','lucky','happy','wild','cool','smart',
                       'eager','fancy','jolly','kind','lively','mighty','noble','proud','quiet','rapid'];
        $nouns      = ['tiger','eagle','river','storm','flame','ocean','comet','forest','thunder','rocket',
                       'falcon','knight','panda','wolf','shark','phoenix','cobra','viper','lynx','bear'];
        do {
            $code = $adjectives[array_rand($adjectives)] . '-' . $nouns[array_rand($nouns)] . '-' . rand(10, 99);
        } while (Room::where('join_code', $code)->exists());
        return $code;
    }
}
