<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use BelongsToOrganization, HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'assigned_user_id',
        'team_id',
        'lead_source_id',
        'lead_status_id',
        'first_name',
        'last_name',
        'company',
        'designation',
        'email',
        'secondary_email',
        'phone',
        'mobile',
        'website',
        'industry',
        'annual_revenue',
        'employee_count',
        'rating',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'description',
        'is_converted',
        'converted_at',
        'converted_contact_id',
        'converted_account_id',
        'converted_deal_id',
    ];

    protected function casts(): array
    {
        return [
            'annual_revenue' => 'decimal:2',
            'employee_count' => 'integer',
            'is_converted' => 'boolean',
            'converted_at' => 'datetime',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function convertedContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'converted_contact_id');
    }

    public function convertedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'converted_account_id');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subjectable');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
