<?php

namespace App\Http\Controllers;

use App\Actions\GameDeleteAction;
use App\Actions\GameStoreAction;
use App\Actions\GameUpdateAction;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function index(): Response|JsonResponse
    {
        $this->authorize('viewAny', Game::class);

        $games = Game::where('user_id', auth()->id())->get();

        if (request()->wantsJson()) {
            return response()->json($games);
        }

        return Inertia::render('Games/Index', [
            'games' => $games,
        ]);
    }

    public function store(StoreGameRequest $request, GameStoreAction $action): RedirectResponse
    {
        $this->authorize('create', Game::class);

        $action->execute($request, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game added.')]);

        return to_route('games.index');
    }

    public function update(UpdateGameRequest $request, Game $game, GameUpdateAction $action): RedirectResponse
    {
        $this->authorize('update', $game);

        $action->execute($request, $game);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game updated.')]);

        return to_route('games.index');
    }

    public function destroy(Game $game, GameDeleteAction $action): RedirectResponse
    {
        $this->authorize('delete', $game);

        $action->execute($game);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game deleted.')]);

        return to_route('games.index');
    }
}
