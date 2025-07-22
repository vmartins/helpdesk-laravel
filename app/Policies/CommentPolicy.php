<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Settings\TicketSettings;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Ticket $ticket): bool
    {
        if ($ticket) {
            return !in_array($ticket->ticketStatus->id, app(TicketSettings::class)->closed_status);
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        // The admin unit can update tickets that are assigned to their specific unit.
        if ($user->hasRole('Admin Unit')) {
            return $user->id == $comment->user_id || $comment->ticket->unit_id == $user->unit_id;
        }

        // The staff unit can update tickets that have been assigned to them.
        if ($user->hasRole('Staff Unit')) {
            return $user->id == $comment->user_id || $comment->ticket->responsible_id == $user->id;
        }

        // The user can view their own ticket
        return $user->id == $comment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->can('delete Comment');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->can('restore Comment');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->can('force-delete Comment');
    }
}
