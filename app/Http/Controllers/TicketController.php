<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $status = is_string($status) && in_array($status, Ticket::STATUSES, true) ? $status : null;

        $tickets = Ticket::with('category')
            ->status($status)
            ->latest()
            ->get();

        return view('tickets.index', [
            'tickets' => $tickets,
            'categories' => Category::orderBy('name')->get(),
            'currentStatus' => $status,
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load('category');

        return view('tickets.show', ['ticket' => $ticket]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::create($request->validated());

        return redirect()->route('tickets.show', $ticket)->with('status', 'チケットを登録しました。');
    }
}
