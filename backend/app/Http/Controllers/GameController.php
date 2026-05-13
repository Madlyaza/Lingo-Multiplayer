<?php

namespace App\Http\Controllers;

use App\Helpers\WordList;
use App\Models\Game;
use App\Models\Room;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Start a new game in a room (host only).
     */
    public function store(Request $request, Room $room)
    {
        if ($request->user()->id !== $room->host_id) {
            return response()->json(['message' => 'Only the host can start the game.'], 403);
        }

        if (! $room->guest_id) {
            return response()->json(['message' => 'Waiting for a second player to join.'], 422);
        }

        if ($room->games()->where('status', 'in_progress')->exists()) {
            return response()->json(['message' => 'A game is already in progress.'], 422);
        }

        // Randomly decide who goes first
        $players    = [$room->host_id, $room->guest_id];
        $firstPlayer = $players[array_rand($players)];

        $game = Game::create([
            'room_id'              => $room->id,
            'current_word'         => WordList::random(),
            'round'                => 1,
            'player1_id'           => $room->host_id,
            'player2_id'           => $room->guest_id,
            'player1_score'        => 0,
            'player2_score'        => 0,
            'current_turn_user_id' => $firstPlayer,
            'round_first_user_id'  => $firstPlayer,
            'status'               => 'in_progress',
        ]);

        $room->update(['status' => 'in_progress']);

        return response()->json($this->formatGame($game), 201);
    }

    /**
     * Get current game state.
     */
    public function show(Game $game)
    {
        return response()->json($this->formatGame($game));
    }

    /**
     * Submit a guess.
     */
    public function guess(Request $request, Game $game)
    {
        $userId = $request->user()->id;

        if ($userId !== $game->player1_id && $userId !== $game->player2_id) {
            return response()->json(['message' => 'You are not a player in this game.'], 403);
        }

        if ($game->status !== 'in_progress') {
            return response()->json(['message' => 'This game is no longer in progress.'], 422);
        }

        if ($userId !== $game->current_turn_user_id) {
            return response()->json(['message' => 'It is not your turn.'], 422);
        }

        $request->validate([
            'word' => ['required', 'string', 'size:5', 'alpha'],
        ]);

        $word      = strtoupper($request->word);
        $feedback  = $this->buildFeedback($word, $game->current_word);
        $isCorrect = $word === $game->current_word;

        $guess = $game->guesses()->create([
            'user_id'    => $userId,
            'round'      => $game->round,
            'word'       => $word,
            'feedback'   => $feedback,
            'is_correct' => $isCorrect,
        ]);

        $result = [
            'is_correct'    => $isCorrect,
            'round_over'    => false,
            'game_over'     => false,
            'revealed_word' => null,
        ];

        if ($isCorrect) {
            $result['revealed_word'] = $game->current_word;

            // Award point
            if ($userId === $game->player1_id) {
                $game->player1_score++;
            } else {
                $game->player2_score++;
            }

            if ($game->player1_score >= Game::WINNING_SCORE || $game->player2_score >= Game::WINNING_SCORE) {
                $game->status    = 'finished';
                $game->winner_id = $userId;
                $game->save();
                $result['game_over']  = true;
                $result['round_over'] = true;
            } else {
                $this->startNewRound($game);
                $result['round_over'] = true;
            }
        } else {
            $roundGuessCount = $game->guesses()->where('round', $game->round)->count();

            if ($roundGuessCount >= Game::MAX_GUESSES) {
                $result['revealed_word'] = $game->current_word;
                $result['round_over']    = true;
                $this->startNewRound($game);
            } else {
                // Switch turn to opponent
                $game->current_turn_user_id = $game->opponentOf($userId);
                $game->save();
            }
        }

        $game->refresh();

        return response()->json(array_merge($result, ['game' => $this->formatGame($game)]));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function startNewRound(Game $game): void
    {
        $nextFirst = $game->opponentOf($game->round_first_user_id);

        $game->round++;
        $game->current_word         = WordList::random();
        $game->current_turn_user_id = $nextFirst;
        $game->round_first_user_id  = $nextFirst;
        $game->save();
    }

    private function buildFeedback(string $guess, string $answer): array
    {
        $feedback  = array_fill(0, 5, 'absent');
        $answerArr = str_split($answer);
        $guessArr  = str_split($guess);
        $used      = array_fill(0, 5, false);
        $matched   = array_fill(0, 5, false);

        // Pass 1: exact matches
        for ($i = 0; $i < 5; $i++) {
            if ($guessArr[$i] === $answerArr[$i]) {
                $feedback[$i] = 'correct';
                $used[$i]     = true;
                $matched[$i]  = true;
            }
        }

        // Pass 2: present but wrong position
        for ($i = 0; $i < 5; $i++) {
            if ($matched[$i]) continue;
            for ($j = 0; $j < 5; $j++) {
                if (! $used[$j] && $guessArr[$i] === $answerArr[$j]) {
                    $feedback[$i] = 'present';
                    $used[$j]     = true;
                    break;
                }
            }
        }

        return $feedback;
    }

    private function formatGame(Game $game): array
    {
        $game->load(['player1:id,name', 'player2:id,name', 'winner:id,name']);

        $data = $game->toArray();
        $data['word_hint']     = $game->word_hint;
        $data['current_word']  = null; // never expose to client during play

        $guesses = $game->guesses()
            ->where('round', $game->round)
            ->with('user:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn($g) => array_merge($g->toArray(), ['user_name' => $g->user->name]));

        $data['current_guesses'] = $guesses;

        return $data;
    }
}
