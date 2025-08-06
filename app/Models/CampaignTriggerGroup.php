<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignTriggerGroup extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignTriggerGroupFactory> */
    use HasFactory;
    protected $fillable = [
        'campaign_id',
        'logic',
        'name',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    /**
     * Get the campaign that owns this trigger group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the campaign triggers for this trigger group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CampaignTrigger, $this>
     */
    public function campaignTriggers(): HasMany
    {
        return $this->hasMany(CampaignTrigger::class, 'campaign_trigger_group_id')->orderBy('order_index');
    }

    /**
     * Scope to order groups by their index
     *
     * @param \Illuminate\Database\Eloquent\Builder<CampaignTriggerGroup> $query
     * @return \Illuminate\Database\Eloquent\Builder<CampaignTriggerGroup>
     */
    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy('order_index');
    }
}
