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
        'deleted_at' => 'datetime',
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
        'deleted_by',
    ];

    /**
     * @return BelongsToMany<Market, $this>
     */
    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class);
    }
}
