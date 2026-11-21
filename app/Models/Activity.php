<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'assigned_user_id',
        'subjectable_type',
        'subjectable_id',
        'activity_type',
        'subject',
        'description',
        'priority',
        'status',
        'start_time',
        'due_date',
        'duration_minutes',
        'location',
        'call_type',
        'call_result',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function subjectable(): MorphTo
    {
        return $this->morphTo();
    }
}
