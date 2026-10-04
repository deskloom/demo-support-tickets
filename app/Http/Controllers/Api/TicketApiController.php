<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $status = $request->query('status');
        $status = is_string($status) && in_array($status, Ticket::STATUSES, true) ? $status : null;

        $tickets = Ticket::with('category')
            ->status($status)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::create($request->validated());

        return (new TicketResource($ticket->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        $ticket->update($request->validated());

        return new TicketResource($ticket->load('category'));
    }
}
