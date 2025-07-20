<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignTrigger extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignTriggerFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'campaign_triggers';

    protected $fillable = [
        'campaign_id',
        'type',
        'value',
        'operator',
    ];

    /**
     * @phpstan-return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}