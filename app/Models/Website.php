<?php

namespace App\Models;

use App\Enums\WebsiteTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    /** @use HasFactory<\Database\Factories\WebsiteFactory> */
    use SoftDeletes, HasFactory;
    protected $casts = [
        'type' => WebsiteTypeEnum::class,
    ];

    protected $table = 'websites';

    protected $fillable = [
        'url',
        'api_url',
        'type',
        'auth_type',
        'auth_token',
        'auth_user',
        'auth_pass',
    ];

    /**
     * @return BelongsToMany<Market, $this>
     */
    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class);
    }

        /**
     * @phpstan-return HasMany<CampaignWebsite>
     */
    public function campaignWebsites(): HasMany
    {
        return $this->hasMany(CampaignWebsite::class);
    }
    
    /**
     * Get campaigns through the pivot relationship
     */
    public function campaigns()
    {
        return $this->hasManyThrough(
            Campaign::class,
            CampaignWebsite::class,
            'website_id',
            'id',
            'id',
            'campaign_id'
        );
    }
}