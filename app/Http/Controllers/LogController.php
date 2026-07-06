<?php

namespace App\Http\Controllers;

use App\Actions\LogStoreAction;
use App\Http\Requests\LogStoreRequest;
use App\Models\Kid;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class LogController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Log::class);

        $logs = Log::with('kid')
            ->where('user_id', auth()->id())
            ->latest('log_date')
            ->paginate(20);

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
        ]);
    }

    public function store(LogStoreRequest $request, LogStoreAction $action): JsonResponse
    {
        $kid = Kid::findOrFail($request->input('kid_id'));

        $this->authorize('createForKid', [Log::class, $kid]);

        $log = $action->execute($request, $request->user());

        return response()->json($log, 201);
    }
}
