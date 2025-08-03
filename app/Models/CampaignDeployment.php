<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDeployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'status',
        'deployed_at',
        'metadata',
    ];

    protected $casts = [
        'deployed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}