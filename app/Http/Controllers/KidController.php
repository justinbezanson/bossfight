<?php

namespace App\Http\Controllers;

use App\Actions\KidDeleteAction;
use App\Actions\KidStoreAction;
use App\Actions\KidUpdateAction;
use App\Http\Requests\KidStoreRequest;
use App\Http\Requests\KidUpdateRequest;
use App\Models\Kid;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KidController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Kid::class);

        $kids = Kid::where('user_id', auth()->id())->get();

        return Inertia::render('Kids/Index', [
            'kids' => $kids,
        ]);
    }

    public function store(KidStoreRequest $request, KidStoreAction $action): RedirectResponse
    {
        $this->authorize('create', Kid::class);

        $action->execute($request, $request->user());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Kid added.')]);

        return to_route('kids.index');
    }

    public function update(KidUpdateRequest $request, Kid $kid, KidUpdateAction $action): RedirectResponse
    {
        $this->authorize('update', $kid);

        $action->execute($request, $kid);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Kid updated.')]);

        return to_route('kids.index');
    }

    public function destroy(Kid $kid, KidDeleteAction $action): RedirectResponse
    {
        $this->authorize('delete', $kid);

        $action->execute($kid);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Kid deleted.')]);

        return to_route('kids.index');
    }
}
