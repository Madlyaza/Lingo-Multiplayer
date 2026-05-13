<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupInactiveRooms extends Command
{
    protected $signature   = 'rooms:cleanup';
    protected $description = 'Delete rooms that have had no activity for 10 minutes';

    public function handle(): int
    {
        $cutoff = now()->subMinutes(10);

        // 1. Waiting rooms with no activity for 10 minutes
        //    (updated_at covers both creation and guest joining)
        $waitingIds = Room::where('status', 'waiting')
            ->where('updated_at', '<', $cutoff)
            ->pluck('id');

        // 2. In-progress rooms whose active game has had no guesses for 10 minutes
        //    (or was started 10+ minutes ago with no guesses at all)
        $inProgressIds = Room::where('status', 'in_progress')
            ->whereHas('games', function ($q) use ($cutoff) {
                $q->where('status', 'in_progress')
                  ->where(function ($q2) use ($cutoff) {
                      // No guesses exist for the game, and it started > 10 min ago
                      $q2->whereDoesntHave('guesses')
                         ->where('games.created_at', '<', $cutoff);
                  })
                  ->orWhere(function ($q2) use ($cutoff) {
                      // Has guesses, but the most recent one is older than the cutoff
                      $q2->whereHas('guesses', function ($q3) use ($cutoff) {
                          $q3->where('created_at', '<', $cutoff);
                      })
                      ->whereDoesntHave('guesses', function ($q3) use ($cutoff) {
                          $q3->where('created_at', '>=', $cutoff);
                      });
                  });
            })
            ->pluck('id');

        $allIds = $waitingIds->merge($inProgressIds)->unique();

        if ($allIds->isEmpty()) {
            $this->info('No inactive rooms found.');
            return self::SUCCESS;
        }

        // Deleting rooms cascades to games → guesses automatically
        $deleted = Room::whereIn('id', $allIds)->delete();
        $this->info("Cleaned up {$deleted} inactive room(s).");

        return self::SUCCESS;
    }
}
