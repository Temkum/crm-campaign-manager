<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'end_at' => 'datetime'
    ];

    /**
     * @phpstan-return BelongsTo<Operator, $this>
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * @phpstan-return BelongsTo<Market, $this>
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * @phpstan-return HasMany<CampaignWebsite>
     */
    public function campaignWebsites(): HasMany
    {
        return $this->hasMany(CampaignWebsite::class);
    }

    /**
     * @phpstan-return HasMany<CampaignTrigger>
     */
    public function campaignTriggers(): HasMany
    {
        return $this->hasMany(CampaignTrigger::class);
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
            $campaign->campaignTriggers()->delete();
        });
    }
}