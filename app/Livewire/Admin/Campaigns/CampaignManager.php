<?php

namespace App\Livewire\Admin\Campaigns;

use App\Models\Market;
use App\Models\Website;
use Livewire\Component;
use App\Models\Campaign;
use App\Models\Operator;
use App\Models\CampaignTrigger;
use App\Models\CampaignWebsite;
use App\Models\CampaignTriggerGroup;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use App\Enums\CampaignStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class CampaignManager extends Component
{
    // Campaign core fields
    public $campaign;
    public $name = '';
    public $operator_id = '';
    public $market_id = '';
    public $start_at = '';
    public $end_at = '';
    public $status = 'disabled';
    public $priority = 1;
    public $duration = '';
    public $rotation_delay = '';
    public $dom_selector = '';

    // Campaign websites
    public $websites = [];

    // Campaign trigger groups and global logic
    public $globalLogic = 'AND';

    /** @var array<int, array{id: ?int, name: string, logic: string, order_index: int, triggers: array<int, array{id: ?int, type: string, operator: string, value: string, description: string, order_index: int}>}> */
    public $groups = [];

    // Available options for dropdowns
    public $operators = [];
    public $markets = [];
    public $availableWebsites = [];
    public $status_options = [];

    // Trigger dropdown options
    public $triggerTypes = [
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

    public $triggerOperators = [
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

    // UI state
    public $isEdit = false;
    public $campaign_id = null;

    protected function rules()
    {
        return [
            // Campaign core validation
            'name' => 'required|string|max:255',
            'operator_id' => 'required|exists:operators,id',
            'market_id' => 'required|exists:markets,id',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'status' => ['nullable', Rule::in(array_keys($this->status_options))],
            'priority' => 'required|integer|min:1|max:10',
            'duration' => 'nullable|integer|min:1',
            'rotation_delay' => 'nullable|integer|min:0',
            'dom_selector' => 'nullable|string|max:255',

            // Global logic validation
            'globalLogic' => ['required', Rule::in(['AND', 'OR'])],

            // Campaign websites validation
            'websites' => 'array|min:1',
            'websites.*.website_id' => 'required|exists:websites,id',
            'websites.*.priority' => 'required|integer|min:1|max:10',
            'websites.*.dom_selector' => 'required|string|max:255',
            'websites.*.custom_affiliate_url' => 'nullable|url|max:500',
            'websites.*.timer_offset' => 'required|integer|min:1',

            // Campaign trigger groups validation
            'groups' => 'array|min:1',
            'groups.*.name' => 'required|string|max:255',
            'groups.*.logic' => ['required', Rule::in(['AND', 'OR'])],
            'groups.*.order_index' => 'required|integer|min:0',
            'groups.*.triggers' => 'array|min:1',

            // Campaign triggers validation
            'groups.*.triggers.*.type' => ['required', Rule::in(array_keys($this->triggerTypes))],
            'groups.*.triggers.*.operator' => ['required', Rule::in(array_keys($this->triggerOperators))],
            'groups.*.triggers.*.value' => 'required|string|max:255',
            'groups.*.triggers.*.description' => 'nullable|string|max:500',
            'groups.*.triggers.*.order_index' => 'required|integer|min:0',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Campaign name is required.',
            'operator_id.required' => 'Please select an operator.',
            'market_id.required' => 'Please select a market.',
            'start_at.required' => 'Start date is required.',
            'start_at.after_or_equal' => 'Start date must be today or later.',
            'end_at.required' => 'End date is required.',
            'end_at.after' => 'End date must be after start date.',
            'globalLogic.required' => 'Global logic is required.',
            'websites.min' => 'At least one website is required.',
            'websites.*.website_id.required' => 'Please select a website.',
            'websites.*.priority.required' => 'Website priority is required.',
            'websites.*.priority.min' => 'Priority must be at least 1.',
            'websites.*.priority.max' => 'Priority cannot exceed 10.',
            'websites.*.custom_affiliate_url.url' => 'Please enter a valid URL.',
            'websites.*.timer_offset.required' => 'Timer offset is required.',
            'websites.*.timer_offset.min' => 'Timer offset must be at least 1.',
            'websites.*.dom_selector.required' => 'DOM selector is required.',
            'groups.min' => 'At least one trigger group is required.',
            'groups.*.name.required' => 'Group name is required.',
            'groups.*.logic.required' => 'Group logic is required.',
            'groups.*.triggers.min' => 'Each group must have at least one trigger.',
            'groups.*.triggers.*.type.required' => 'Trigger type is required.',
            'groups.*.triggers.*.operator.required' => 'Trigger operator is required.',
            'groups.*.triggers.*.value.required' => 'Trigger value is required.',
        ];
    }

    public function mount($campaign_id = null)
    {
        try {
            $this->campaign_id = $campaign_id;
            $this->loadDropdownData();

            if ($campaign_id) {
                Log::info('Loading campaign for edit', ['campaign_id' => $campaign_id]);
                $this->isEdit = true;
                $this->loadCampaign($campaign_id);
                Log::info('Campaign loaded successfully', [
                    'groups_count' => count($this->groups),
                    'websites_count' => count($this->websites)
                ]);
            } else {
                $this->initializeEmptyForm();
            }
        } catch (\Exception $e) {
            Log::error('Mount error: ' . $e->getMessage(), [
                'campaign_id' => $campaign_id,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Failed to load campaign data.');
            if ($campaign_id) {
                return redirect()->route('campaigns.index');
            }
        }
    }

    protected function loadDropdownData()
    {
        $this->operators = Operator::select('id', 'name')->orderBy('name')->get()->toArray();
        $this->markets = Market::select('id', 'name')->orderBy('name')->get()->toArray();
        $this->availableWebsites = Website::select('id', 'url', 'type')->orderBy('url')->get()->toArray();
        $this->status_options = CampaignStatusEnum::getSelectOptions();
    }

    protected function loadCampaign(int $campaign_id)
    {
        try {
            $this->campaign = Campaign::with([
                'campaignWebsites.website',
                'campaignTriggerGroups.campaignTriggers'
            ])->findOrFail($campaign_id);

            // Load campaign core fields with null coalescence
            $this->name = $this->campaign->name ?? '';
            $this->operator_id = (string) ($this->campaign->operator_id ?? '');
            $this->market_id = (string) ($this->campaign->market_id ?? '');

            // Safe date formatting
            $this->start_at = $this->campaign->start_at
                ? $this->campaign->start_at->format('Y-m-d\TH:i')
                : '';
            $this->end_at = $this->campaign->end_at
                ? $this->campaign->end_at->format('Y-m-d\TH:i')
                : '';

            $this->status = $this->campaign->status ?? 'disabled';
            $this->priority = $this->campaign->priority ?? 1;
            $this->duration = $this->campaign->duration ? (string) $this->campaign->duration : '';
            $this->rotation_delay = $this->campaign->rotation_delay ? (string) $this->campaign->rotation_delay : '';
            $this->dom_selector = $this->campaign->dom_selector ?? '';
            $this->globalLogic = $this->campaign->global_logic ?? 'AND';

            // Load websites with safety checks
            $this->loadCampaignWebsites();

            // Load trigger groups with safety checks
            $this->loadCampaignTriggerGroups();
        } catch (\Exception $e) {
            Log::error('Campaign loading error: ' . $e->getMessage(), [
                'campaign_id' => $campaign_id,
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Campaign not found or could not be loaded.');
            return redirect()->route('campaigns.index');
        }
    }

    protected function initializeEmptyForm()
    {
        $this->initializeEmptyWebsites();
        $this->initializeEmptyGroups();
    }

    protected function initializeEmptyWebsites()
    {
        $this->websites = [
            [
                'website_id' => '',
                'priority' => 1,
                'dom_selector' => '',
                'custom_affiliate_url' => '',
                'timer_offset' => '',
            ]
        ];
    }

    protected function initializeEmptyGroups()
    {
        $this->groups = [
            [
                'id' => null,
                'name' => '',
                'logic' => 'AND',
                'order_index' => 0,
                'triggers' => [
                    [
                        'id' => null,
                        'type' => '',
                        'operator' => '',
                        'value' => '',
                        'description' => '',
                        'order_index' => 0,
                    ]
                ]
            ]
        ];
    }

    // Website management methods
    public function addWebsite()
    {
        $this->websites[] = [
            'website_id' => '',
            'priority' => 1,
            'dom_selector' => '',
            'custom_affiliate_url' => '',
            'timer_offset' => '',
        ];
    }

    public function removeWebsite($index)
    {
        if (count($this->websites) > 1) {
            unset($this->websites[$index]);
            $this->websites = array_values($this->websites);
        }
    }

    // Trigger group management methods
    public function addGroup()
    {
        $maxOrderIndex = 0;
        foreach ($this->groups as $group) {
            if ($group['order_index'] > $maxOrderIndex) {
                $maxOrderIndex = $group['order_index'];
            }
        }

        $this->groups[] = [
            'id' => null,
            'name' => '',
            'logic' => 'AND',
            'order_index' => $maxOrderIndex + 1,
            'triggers' => [
                [
                    'id' => null,
                    'type' => '',
                    'operator' => '',
                    'value' => '',
                    'description' => '',
                    'order_index' => 0,
                ]
            ]
        ];
    }

    public function removeGroup($groupIndex)
    {
        if (count($this->groups) > 1 && isset($this->groups[$groupIndex])) {
            // Remove the group
            array_splice($this->groups, $groupIndex, 1);

            // Reorder indices properly
            $this->reorderGroups();
        }
    }

    private function reorderGroups()
    {
        foreach ($this->groups as $index => &$group) {
            $group['order_index'] = $index;
        }
        unset($group); // Break reference
    }

    public function moveGroupUp($groupIndex)
    {
        if ($groupIndex > 0) {
            $temp = $this->groups[$groupIndex];
            $this->groups[$groupIndex] = $this->groups[$groupIndex - 1];
            $this->groups[$groupIndex - 1] = $temp;

            // Update order indices
            $this->groups[$groupIndex]['order_index'] = $groupIndex;
            $this->groups[$groupIndex - 1]['order_index'] = $groupIndex - 1;
        }
    }

    public function moveGroupDown($groupIndex)
    {
        if ($groupIndex < count($this->groups) - 1) {
            $temp = $this->groups[$groupIndex];
            $this->groups[$groupIndex] = $this->groups[$groupIndex + 1];
            $this->groups[$groupIndex + 1] = $temp;

            // Update order indices
            $this->groups[$groupIndex]['order_index'] = $groupIndex;
            $this->groups[$groupIndex + 1]['order_index'] = $groupIndex + 1;
        }
    }

    // Trigger management methods
    public function addTrigger($groupIndex)
    {
        $maxOrderIndex = 0;
        foreach ($this->groups[$groupIndex]['triggers'] as $trigger) {
            if ($trigger['order_index'] > $maxOrderIndex) {
                $maxOrderIndex = $trigger['order_index'];
            }
        }

        $this->groups[$groupIndex]['triggers'][] = [
            'id' => null,
            'type' => '',
            'operator' => '',
            'value' => '',
            'description' => '',
            'order_index' => $maxOrderIndex + 1,
        ];
    }

    public function removeTrigger($groupIndex, $triggerIndex)
    {
        if (
            isset($this->groups[$groupIndex]) &&
            count($this->groups[$groupIndex]['triggers']) > 1 &&
            isset($this->groups[$groupIndex]['triggers'][$triggerIndex])
        ) {

            // Remove the trigger
            array_splice($this->groups[$groupIndex]['triggers'], $triggerIndex, 1);

            // Reorder triggers
            $this->reorderTriggers($groupIndex);
        }
    }

    private function reorderTriggers($groupIndex)
    {
        if (isset($this->groups[$groupIndex])) {
            foreach ($this->groups[$groupIndex]['triggers'] as $index => &$trigger) {
                $trigger['order_index'] = $index;
            }
            unset($trigger); // Break reference
        }
    }

    public function moveTriggerUp($groupIndex, $triggerIndex)
    {
        if ($triggerIndex > 0) {
            $temp = $this->groups[$groupIndex]['triggers'][$triggerIndex];
            $this->groups[$groupIndex]['triggers'][$triggerIndex] = $this->groups[$groupIndex]['triggers'][$triggerIndex - 1];
            $this->groups[$groupIndex]['triggers'][$triggerIndex - 1] = $temp;

            // Update order indices
            $this->groups[$groupIndex]['triggers'][$triggerIndex]['order_index'] = $triggerIndex;
            $this->groups[$groupIndex]['triggers'][$triggerIndex - 1]['order_index'] = $triggerIndex - 1;
        }
    }

    public function moveTriggerDown($groupIndex, $triggerIndex)
    {
        if ($triggerIndex < count($this->groups[$groupIndex]['triggers']) - 1) {
            $temp = $this->groups[$groupIndex]['triggers'][$triggerIndex];
            $this->groups[$groupIndex]['triggers'][$triggerIndex] = $this->groups[$groupIndex]['triggers'][$triggerIndex + 1];
            $this->groups[$groupIndex]['triggers'][$triggerIndex + 1] = $temp;

            // Update order indices
            $this->groups[$groupIndex]['triggers'][$triggerIndex]['order_index'] = $triggerIndex;
            $this->groups[$groupIndex]['triggers'][$triggerIndex + 1]['order_index'] = $triggerIndex + 1;
        }
    }

    // Live validation methods
    public function updated($propertyName)
    {
        // Skip validation during array manipulations to prevent conflicts
        if ($this->isArrayManipulation($propertyName)) {
            return;
        }

        try {
            $this->validateOnly($propertyName);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            Log::debug('Validation error for property: ' . $propertyName, [
                'errors' => $e->errors()
            ]);
        }
    }

    /*  private function isArrayManipulation($propertyName)
    {
        // Skip validation for certain operations that might cause conflicts
        $skipPatterns = [
            'groups.*.order_index',
            'groups.*.triggers.*.order_index',
            'websites.*.id'
        ];

        foreach ($skipPatterns as $pattern) {
            if (fnmatch($pattern, $propertyName)) {
                return true;
            }
        }

        return false;
    } */

    private function isArrayManipulation($propertyName)
    {
        // Skip validation for certain operations that might cause conflicts
        $skipPatterns = [
            'groups.*.order_index',
            'groups.*.triggers.*.order_index',
            'websites.*.id'
        ];

        foreach ($skipPatterns as $pattern) {
            if (fnmatch($pattern, $propertyName)) {
                return true;
            }
        }

        return false;
    }

    public function submit()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('scrollToFirstError');
            throw $e;
        }

        try {
            DB::transaction(function () {
                if ($this->isEdit) {
                    $this->updateCampaign();
                } else {
                    $this->createCampaign();
                }
            });

            $message = $this->isEdit ? 'Campaign updated successfully!' : 'Campaign created successfully!';
            session()->flash('success', $message);

            return redirect()->route('campaigns.index');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while saving the campaign. Please try again.');
            Log::error($e);
            $this->dispatch('scrollToFirstError');
        }
    }

    protected function createCampaign()
    {
        $campaign = Campaign::create([
            'name' => $this->name,
            'operator_id' => $this->operator_id,
            'market_id' => $this->market_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'status' => $this->status,
            'priority' => $this->priority,
            'duration' => $this->duration ?: null,
            'rotation_delay' => $this->rotation_delay ?: null,
            'dom_selector' => $this->dom_selector ?: null,
            'global_logic' => $this->globalLogic,
        ]);

        $this->saveCampaignWebsites($campaign);
        $this->saveCampaignTriggerGroups($campaign);
    }

    protected function updateCampaign()
    {
        $this->campaign->update([
            'name' => $this->name,
            'operator_id' => $this->operator_id,
            'market_id' => $this->market_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'status' => $this->status,
            'priority' => $this->priority,
            'duration' => $this->duration ?: null,
            'rotation_delay' => $this->rotation_delay ?: null,
            'dom_selector' => $this->dom_selector ?: null,
            'global_logic' => $this->globalLogic,
        ]);

        // Delete existing relationships and recreate them
        $this->campaign->campaignWebsites()->delete();
        $this->campaign->campaignTriggerGroups()->each(function ($group) {
            $group->campaignTriggers()->delete();
        });
        $this->campaign->campaignTriggerGroups()->delete();

        $this->saveCampaignWebsites($this->campaign);
        $this->saveCampaignTriggerGroups($this->campaign);
    }

    protected function saveCampaignWebsites($campaign)
    {
        foreach ($this->websites as $website) {
            if (!empty($website['website_id'])) {
                CampaignWebsite::create([
                    'campaign_id' => $campaign->id,
                    'website_id' => $website['website_id'],
                    'priority' => $website['priority'],
                    'dom_selector' => $website['dom_selector'] ?: null,
                    'custom_affiliate_url' => $website['custom_affiliate_url'] ?: null,
                    'timer_offset' => $website['timer_offset'] ?: null,
                ]);
            }
        }
    }

    protected function saveCampaignTriggerGroups($campaign)
    {
        foreach ($this->groups as $groupData) {
            if (!empty($groupData['name'])) {
                $group = CampaignTriggerGroup::create([
                    'campaign_id' => $campaign->id,
                    'name' => $groupData['name'],
                    'logic' => $groupData['logic'],
                    'order_index' => $groupData['order_index'],
                ]);

                foreach ($groupData['triggers'] as $triggerData) {
                    if (!empty($triggerData['type']) && !empty($triggerData['operator']) && !empty($triggerData['value'])) {
                        CampaignTrigger::create([
                            'campaign_trigger_group_id' => $group->id,
                            'type' => $triggerData['type'],
                            'operator' => $triggerData['operator'],
                            'value' => $triggerData['value'],
                            'description' => $triggerData['description'] ?: null,
                            'order_index' => $triggerData['order_index'],
                        ]);
                    }
                }
            }
        }
    }

    private function loadCampaignWebsites()
    {
        if ($this->campaign->campaignWebsites && $this->campaign->campaignWebsites->count() > 0) {
            $this->websites = $this->campaign->campaignWebsites->map(function ($website) {
                return [
                    'id' => $website->id,
                    'website_id' => (string) $website->website_id,
                    'priority' => $website->priority ?? 1,
                    'dom_selector' => $website->dom_selector ?? '',
                    'custom_affiliate_url' => $website->custom_affiliate_url ?? '',
                    'timer_offset' => $website->timer_offset ? (string) $website->timer_offset : '1',
                ];
            })->toArray();
        } else {
            $this->initializeEmptyWebsites();
        }
    }

    private function loadCampaignTriggerGroups()
    {
        if ($this->campaign->campaignTriggerGroups && $this->campaign->campaignTriggerGroups->count() > 0) {
            $this->groups = $this->campaign->campaignTriggerGroups
                ->sortBy('order_index')
                ->map(function ($group) {
                    $triggers = [];

                    if ($group->campaignTriggers && $group->campaignTriggers->count() > 0) {
                        $triggers = $group->campaignTriggers
                            ->sortBy('order_index')
                            ->map(function ($trigger) {
                                return [
                                    'id' => $trigger->id,
                                    'type' => $trigger->type ?? '',
                                    'operator' => $trigger->operator ?? '',
                                    'value' => $trigger->value ?? '',
                                    'description' => $trigger->description ?? '',
                                    'order_index' => $trigger->order_index ?? 0,
                                ];
                            })->values()->toArray();
                    } else {
                        // If no triggers exist, create a default empty one
                        $triggers = [
                            [
                                'id' => null,
                                'type' => '',
                                'operator' => '',
                                'value' => '',
                                'description' => '',
                                'order_index' => 0,
                            ]
                        ];
                    }

                    return [
                        'id' => $group->id,
                        'name' => $group->name ?? '',
                        'logic' => $group->logic ?? 'AND',
                        'order_index' => $group->order_index ?? 0,
                        'triggers' => $triggers,
                    ];
                })->values()->toArray();
        } else {
            $this->initializeEmptyGroups();
        }
    }


    public function render()
    {
        return view('livewire.admin.campaigns.campaign-manager');
    }
}
