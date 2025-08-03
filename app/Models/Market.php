<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'country_code',
    ];

    /**
     * @return BelongsToMany<Website, $this>
     */
    public function websites(): BelongsToMany
    {
        return $this->belongsToMany(Website::class);
    }
}
