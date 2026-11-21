<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    /**
     * Boot the trait to scope queries to the current active organization.
     */
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $orgId = static::resolveActiveOrganizationId();
            if ($orgId) {
                $builder->where($builder->getModel()->getTable().'.organization_id', $orgId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = static::resolveActiveOrganizationId();
            }
        });
    }

    /**
     * Resolve the active organization ID from session, user, or request.
     */
    public static function resolveActiveOrganizationId(): ?int
    {
        if (session()->has('active_organization_id')) {
            return (int) session('active_organization_id');
        }

        if (auth()->check()) {
            return auth()->user()->organization_id;
        }

        if (request()->hasHeader('X-Organization-Id')) {
            return (int) request()->header('X-Organization-Id');
        }

        return null;
    }

    /**
     * Relationship to the Organization model.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
