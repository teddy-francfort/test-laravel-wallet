<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateRecurringTransferRequest;
use App\Models\RecurringTransfer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RecurringTransferController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $recurringTransfers = $request->user()->recurringTransfers()->orderByDesc('id')->get();

        return view('recurring_transfers', compact('recurringTransfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRecurringTransferRequest $request)
    {
        $data = array_merge($request->validated(), ['user_id' => $request->user()->id]);
        $recurringTransfer = RecurringTransfer::query()->create($data);

        return ($request->expectsJson()) ?
            new JsonResponse($recurringTransfer, JsonResponse::HTTP_CREATED)
            : redirect()->back()->with('recurring-transfer-status', 'created');
    }

    /**
     * Display the specified resource.
     */
    public function show(RecurringTransfer $recurringTransfer)
    {
        Gate::authorize('view', $recurringTransfer);

        return new JsonResponse($recurringTransfer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, RecurringTransfer $recurringTransfer)
    {
        Gate::authorize('delete', $recurringTransfer);

        $recurringTransfer->delete();

        return ($request->expectsJson()) ?
            new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT)
            : redirect()->back()->with('recurring-transfer-status', 'deleted');
    }
}
