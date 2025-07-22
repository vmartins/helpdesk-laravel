<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Priority extends Model
{
    use LogsActivity;
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sla',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                '*',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
