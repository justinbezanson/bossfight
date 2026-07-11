<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $logs = Log::with(['kid', 'game'])
            ->where('user_id', auth()->id())
            ->whereNotNull('game_id')
            ->orderBy('kid_id')
            ->orderBy('game_id')
            ->orderBy('log_date')
            ->get();

        $sessions = $this->calculateSessions($logs);

        $topGames = $this->getTopGames($sessions);
        $recentSessions = $this->getRecentSessions($sessions);

        return Inertia::render('Dashboard', [
            'topGames' => $topGames,
            'recentSessions' => $recentSessions,
        ]);
    }

    /**
     * @param  Collection<int, Log>  $logs
     * @return Collection<int, array{game: mixed, kid: mixed, start: Carbon, end: Carbon, duration_minutes: float}>
     */
    private function calculateSessions(Collection $logs): Collection
    {
        $grouped = $logs->groupBy(fn (Log $log): string => $log->kid_id.'-'.$log->game_id);

        $sessions = new Collection;

        foreach ($grouped as $group) {
            $currentSession = null;

            foreach ($group as $log) {
                if ($currentSession === null) {
                    $currentSession = [
                        'game' => $log->game,
                        'kid' => $log->kid,
                        'start' => $log->log_date,
                        'end' => $log->log_date,
                    ];
                } else {
                    $gapMinutes = $currentSession['end']->diffInMinutes($log->log_date);

                    if ($gapMinutes > 5) {
                        $sessions->push($this->finalizeSession($currentSession));

                        $currentSession = [
                            'game' => $log->game,
                            'kid' => $log->kid,
                            'start' => $log->log_date,
                            'end' => $log->log_date,
                        ];
                    } else {
                        $currentSession['end'] = $log->log_date;
                    }
                }
            }

            if ($currentSession !== null) {
                $sessions->push($this->finalizeSession($currentSession));
            }
        }

        return $sessions;
    }

    /**
     * @param  array{game: mixed, kid: mixed, start: Carbon, end: Carbon}  $session
     * @return array{game: mixed, kid: mixed, start: Carbon, end: Carbon, duration_minutes: float}
     */
    private function finalizeSession(array $session): array
    {
        return [
            ...$session,
            'duration_minutes' => $session['start']->diffInSeconds($session['end']) / 60,
        ];
    }

    /**
     * @param  Collection<int, array{game: mixed, kid: mixed, start: Carbon, end: Carbon, duration_minutes: float}>  $sessions
     * @return array<int, array{game: mixed, total_minutes: float, session_count: int}>
     */
    private function getTopGames(Collection $sessions): array
    {
        return $sessions
            ->groupBy(fn (array $session): int => $session['game']->id)
            ->map(fn (Collection $gameSessions) => [
                'game' => $gameSessions->first()['game'],
                'total_minutes' => round($gameSessions->sum('duration_minutes'), 1),
                'session_count' => $gameSessions->count(),
            ])
            ->sortByDesc('total_minutes')
            ->values()
            ->take(3)
            ->toArray();
    }

    /**
     * @param  Collection<int, array{game: mixed, kid: mixed, start: Carbon, end: Carbon, duration_minutes: float}>  $sessions
     * @return array<int, array{game: mixed, kid: mixed, start: Carbon, end: Carbon, duration_minutes: float}>
     */
    private function getRecentSessions(Collection $sessions): array
    {
        return $sessions
            ->sortByDesc('end')
            ->values()
            ->take(10)
            ->toArray();
    }
}
