<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\TicketSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTicketController extends Controller
{
    public function create(TicketSettings $ticketSettings, GeneralSettings $generalSettings): View
    {
        $categories = Category::with('units')->orderBy('name')->get();

        return view('public-ticket.create', [
            'enabled' => $ticketSettings->public_ticket_creation_enabled,
            'siteTitle' => $generalSettings->site_title,
            'units' => Unit::orderBy('name')->get(),
            'categoriesForSelect' => $categories->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'unit_ids' => $category->units->pluck('id'),
            ])->values(),
        ]);
    }

    public function store(Request $request, TicketSettings $ticketSettings): RedirectResponse
    {
        abort_unless($ticketSettings->public_ticket_creation_enabled, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $guest = User::guest();

        // description is stored as HTML (rendered via a RichEditor), so line
        // breaks must be explicit <br> tags and user input must be escaped.
        $description = implode('<br>', [
            e(__('Name')) . ': ' . e($data['name']),
            e(__('Email')) . ': ' . e($data['email']),
            e(__('Phone')) . ': ' . e($data['phone'] ?: '-'),
            '<br>' . e(__('Message')) . ':',
            nl2br(e($data['message'])),
        ]);

        $ticket = Ticket::create([
            'owner_id' => $guest->id,
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $description,
            'priority_id' => $ticketSettings->default_priority,
            'ticket_statuses_id' => 1,
        ]);

        $ticket->units()->attach([$data['unit_id']]);

        return redirect()
            ->route('public-ticket.create')
            ->with('success', __('Your ticket has been submitted. Our team will get back to you by email soon.'));
    }
}
