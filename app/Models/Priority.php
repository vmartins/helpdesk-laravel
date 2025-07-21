<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Priority.
 *
 * @property int $id
 * @property string $name
 * @property Collection|Ticket[] $tickets
 */
class Priority extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sla',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
