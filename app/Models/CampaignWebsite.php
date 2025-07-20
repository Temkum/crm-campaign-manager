<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignWebsite extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignWebsiteFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_websites';

    protected $fillable = [
        'campaign_id',
        'website_id',
        'priority',
        'dom_selector',
        'custom_affiliate_url',
        'timer_offset',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Website, $this>
     */
    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}