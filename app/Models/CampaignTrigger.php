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
        'campaign_trigger_group_id',
        'type',
        'operator',
        'value',
        'description',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    /**
     * Get the trigger group that owns this trigger.
     */
    public function campaignTriggerGroup(): BelongsTo
    {
        return $this->belongsTo(CampaignTriggerGroup::class);
    }


    /**
     * Get the campaign that owns this trigger.
     
     * @phpstan-return BelongsTo<Campaign, $this>
     */
    public function campaign(): BelongsTo
    {
        return $this->campaignTriggerGroup->campaign();
    }

    /**
     * Scope to order by order_index.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    /**
     * Get available trigger types.
     */
    public static function getAvailableTypes(): array
    {
        return [
            'url' => 'URL',
            'referrer' => 'Referrer',
            'device' => 'Device',
            'country' => 'Country',
            'pageViews' => 'Page Views',
            'timeOnSite' => 'Time on Site',
            'timeOnPage' => 'Time on Page',
            'scroll' => 'Scroll Percentage',
            'exitIntent' => 'Exit Intent',
            'newVisitor' => 'New Visitor',
            'dayOfWeek' => 'Day of Week',
            'hour' => 'Hour',
        ];
    }

    /**
     * Get available operators.
     */
    public static function getAvailableOperators(): array
    {
        return [
            'equals' => 'Equals',
            'contains' => 'Contains',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with',
            'regex' => 'Regular expression',
            'gte' => 'Greater than or equal',
            'lte' => 'Less than or equal',
            'between' => 'Between',
            'in' => 'In (comma separated)',
            'not_in' => 'Not in (comma separated)',
        ];
    }
}
