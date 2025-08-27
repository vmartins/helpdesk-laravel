<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use SoftDeletes;
    use LogsActivity;
    
    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'name',
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

    public function units()
    {
        return $this->morphToMany(Unit::class, 'model', 'model_has_units', 'model_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
