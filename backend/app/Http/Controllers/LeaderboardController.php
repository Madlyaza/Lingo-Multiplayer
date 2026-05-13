<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $rows = DB::table('users')
            ->leftJoin('games as wins', function ($join) {
                $join->on('users.id', '=', 'wins.winner_id')
                     ->where('wins.status', '=', 'finished');
            })
            ->leftJoin('games as played', function ($join) {
                $join->on(function ($q) {
                    $q->on('users.id', '=', 'played.player1_id')
                      ->orOn('users.id', '=', 'played.player2_id');
                })->where('played.status', '=', 'finished');
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT wins.id) as wins'),
                DB::raw('COUNT(DISTINCT played.id) as total_games')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('wins')
            ->orderByDesc('total_games')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                return [
                    'id'          => $row->id,
                    'name'        => $row->name,
                    'wins'        => (int) $row->wins,
                    'losses'      => max(0, (int) $row->total_games - (int) $row->wins),
                    'total_games' => (int) $row->total_games,
                ];
            });

        return response()->json($rows);
    }
}
