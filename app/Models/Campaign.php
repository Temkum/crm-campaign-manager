<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'operator_id',
        'market_id',
        'start_at',
        'end_at',
        'status',
        'priority',
        'duration',
        'rotation_delay',
        'dom_selector',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'priority' => 'integer',
        'duration' => 'integer',
        'rotation_delay' => 'integer',
    ];

    /**
     * Get the operator that owns this campaign.
     *
     * @phpstan-return BelongsTo<Operator, $this>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Get the market that owns this campaign.
     *
     * @phpstan-return BelongsTo<Market, $this>
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get all websites for this campaign.
     *
     * @phpstan-return HasMany<CampaignWebsite>
     */
    public function campaignWebsites(): HasMany
    {
        return $this->hasMany(CampaignWebsite::class);
    }

    /**
     * Get the trigger groups for this campaign.
     *
     * @phpstan-return HasMany<CampaignTriggerGroup>
     */
    public function campaignTriggerGroups(): HasMany
    {
        return $this->hasMany(CampaignTriggerGroup::class)->orderBy('order_index');
    }

    /**
     * Get all deployments for this campaign.
     *
     * @phpstan-return HasMany<CampaignDeployment>
     */
    public function campaignDeployments(): HasMany
    {
        return $this->hasMany(CampaignDeployment::class);
    }

    /**
     * Get all triggers through trigger groups.
     *
     * @phpstan-return HasManyThrough<CampaignTrigger>
     */
    public function campaignTriggers(): HasManyThrough
    {
        return $this->hasManyThrough(
            CampaignTrigger::class,
            CampaignTriggerGroup::class,
            'campaign_id',
            'campaign_trigger_group_id'
        )->orderBy('campaign_trigger_groups.order_index')
            ->orderBy('campaign_triggers.order_index');
    }

    /**
     * Scope for active campaigns.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Boot the model and set up model event listeners.
     */
    protected static function boot(): void
    {
        parent::boot();

        // When deleting a campaign, also delete related websites and triggers
        static::deleting(function (Campaign $campaign) {
            $campaign->campaignWebsites()->delete();
            $campaign->campaignTriggerGroups()->delete();
            $campaign->campaignTriggers()->delete();
        });
    }
}
