<?php

namespace App\Models;

use App\Settings\AccountSettings;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use GeneaLabs\LaravelPivotEvents\Traits\PivotEventTrait;

class Ticket extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use PivotEventTrait;

    protected $casts = [
        'status_updated_at' => 'datetime',
        'internal' => 'boolean',
    ];

    protected $fillable = [
        'priority_id',
        'owner_id',
        'category_id',
        'title',
        'description',
        'ticket_statuses_id',
        'status_updated_at',
        'responsible_id',
        'internal',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        $relationsAttribute = [
            'units' => 'name',
        ];

        $attached = function($model, $relationName, $pivotIds) use ($relationsAttribute) {
            $oldPivotIds = $model->{$relationName}->pluck('id')->diff($pivotIds)->toArray();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($model)
                ->event('updated')
                ->withProperties([
                    'attributes' => [
                        $relationName => array_key_exists($relationName, $relationsAttribute)
                            ? $model->{$relationName}()->get()->pluck($relationsAttribute[$relationName])
                            : $pivotIds,
                    ],
                    'old' => [
                        $relationName => array_key_exists($relationName, $relationsAttribute)
                            ? $model->{$relationName}()->newModelInstance()->whereIn('id', $oldPivotIds)->pluck($relationsAttribute[$relationName])
                            : $oldPivotIds,
                    ],
                ])
                ->log('attached');
        };

        $detached = function($model, $relationName, $pivotIds) use ($relationsAttribute) {
            $oldPivotIds = $model->{$relationName}->pluck('id')->concat($pivotIds)->toArray();

            activity()
                ->causedBy(auth()->user())
                ->performedOn($model)
                ->event('updated')
                ->withProperties([
                    'attributes' => [
                        $relationName => array_key_exists($relationName, $relationsAttribute)
                            ? $model->{$relationName}->pluck($relationsAttribute[$relationName])
                            : $pivotIds,
                    ],
                    'old' => [
                        $relationName => array_key_exists($relationName, $relationsAttribute)
                            ? $model->{$relationName}()->newModelInstance()->whereIn('id', $oldPivotIds)->pluck($relationsAttribute[$relationName])
                            : $oldPivotIds,
                    ],
                ])
                ->log('detached');
        };
        
        static::pivotAttached($attached);
        static::pivotDetached($detached);
        static::pivotSynced(function ($model, $relationName, $changes) use ($attached, $detached) {
            if (!empty($changes['attached'])) {
                $attached($model, $relationName, $changes['attached']);
            }
            
            if (!empty($changes['detached'])) {
                $detached($model, $relationName, $changes['detached']);
            }
        });

        static::saving(function (Ticket $ticket) {
            if (array_key_exists('ticket_statuses_id', $ticket->getDirty())
                && (
                    (
                        array_key_exists('ticket_statuses_id', $ticket->getOriginal())
                        && $ticket->getDirty()['ticket_statuses_id'] != $ticket->getOriginal()['ticket_statuses_id']
                    )
                    || !array_key_exists('ticket_statuses_id', $ticket->getOriginal())
                )
            ) {
                $ticket->status_updated_at = now();
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                '*',
                'priority.name',
                'owner.name',
                'responsible.name',
                'category.name',
                'ticketStatus.name',
                'comments',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getSubscribers(): Collection
    {
        $subscribers = new Collection([]);

        if ($this->owner) {
            $subscribers[$this->owner->id] = $this->owner;
        }

        if ($this->responsible) {
            $subscribers[$this->responsible->id] = $this->responsible;
        }

        $this->comments->each(function($comment) use (&$subscribers) {
            $subscribers->put($comment->user->id, $comment->user);
        });

        $accountSettings = app(AccountSettings::class);
        if ($accountSettings->user_email_verification) {
            $subscribers = $subscribers->filter(function ($subscriber) {
                return $subscriber->email_verified_at;
            });
        }

        return $subscribers;
    }
    
    /**
     * Get the priority that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * Get the units that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function units()
    {
        return $this->morphToMany(Unit::class, 'model', 'model_has_units', 'model_id');
    }

    /**
     * Get the owner that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the responsible that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Get the Category that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the ticketStatus that owns the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticketStatus()
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_statuses_id');
    }

    /**
     * Get all of the comments for the Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'tiket_id');
    }
}
