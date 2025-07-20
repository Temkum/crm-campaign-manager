<?php

namespace App\Livewire\Admin\Campaigns;

use App\Models\Market;
use App\Models\Website;
use Livewire\Component;
use App\Models\Campaign;
use App\Models\Operator;
use App\Models\CampaignTrigger;
use App\Models\CampaignWebsite;
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

    // Campaign triggers
    public $triggers = [];

    // Available options for dropdowns
    public $operators = [];
    public $markets = [];
    public $availableWebsites = [];
    public $status_options = [];

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

            // Campaign websites validation
            'websites' => 'array|min:1',
            'websites.*.website_id' => 'required|exists:websites,id',
            'websites.*.priority' => 'required|integer|min:1|max:10',
            'websites.*.dom_selector' => 'nullable|string|max:255',
            'websites.*.custom_affiliate_url' => 'nullable|url|max:500',
            'websites.*.timer_offset' => 'nullable|integer|min:0',

            // Campaign triggers validation
            'triggers' => 'array|min:1',
            'triggers.*.type' => ['required', Rule::in(['time', 'scroll', 'click', 'exit_intent', 'page_load'])],
            'triggers.*.value' => 'required|string|max:255',
            'triggers.*.operator' => ['required', Rule::in(['equals', 'greater_than', 'less_than', 'contains'])],
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
            'websites.min' => 'At least one website is required.',
            'websites.*.website_id.required' => 'Please select a website.',
            'websites.*.priority.required' => 'Website priority is required.',
            'websites.*.priority.min' => 'Priority must be at least 1.',
            'websites.*.priority.max' => 'Priority cannot exceed 10.',
            'websites.*.custom_affiliate_url.url' => 'Please enter a valid URL.',
            'triggers.min' => 'At least one trigger is required.',
            'triggers.*.type.required' => 'Trigger type is required.',
            'triggers.*.value.required' => 'Trigger value is required.',
            'triggers.*.operator.required' => 'Trigger operator is required.',
        ];
    }

    public function mount($campaign_id = null)
    {
        $this->campaign_id = $campaign_id;
        $this->loadDropdownData();

        if ($campaign_id) {
            $this->isEdit = true;
            $this->loadCampaign($campaign_id);
        } else {
            $this->initializeEmptyForm();
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
            $this->campaign = Campaign::with(['campaignWebsites.website', 'campaignTriggers'])
                ->findOrFail($campaign_id);

            // Load campaign core fields
            $this->name = $this->campaign->name ?? '';
            $this->operator_id = (string) $this->campaign->operator_id;
            $this->market_id = (string) $this->campaign->market_id;
            $this->start_at = $this->campaign->start_at?->format('Y-m-d\TH:i') ?? '';
            $this->end_at = $this->campaign->end_at?->format('Y-m-d\TH:i') ?? '';
            $this->status = $this->campaign->status ?? 'disabled';
            $this->priority = $this->campaign->priority ?? 1;
            $this->duration = $this->campaign->duration ? (string) $this->campaign->duration : '';
            $this->rotation_delay = $this->campaign->rotation_delay ? (string) $this->campaign->rotation_delay : '';
            $this->dom_selector = $this->campaign->dom_selector ?? '';

            // Load websites
            if ($this->campaign->campaignWebsites->count() > 0) {
                $this->websites = $this->campaign->campaignWebsites->map(function ($website) {
                    return [
                        'id' => $website->id,
                        'website_id' => (string) $website->website_id,
                        'priority' => $website->priority,
                        'dom_selector' => $website->dom_selector ?? '',
                        'custom_affiliate_url' => $website->custom_affiliate_url ?? '',
                        'timer_offset' => $website->timer_offset ? (string) $website->timer_offset : '',
                    ];
                })->toArray();
            } else {
                $this->initializeEmptyWebsites();
            }

            // Load triggers
            if ($this->campaign->campaignTriggers->count() > 0) {
                $this->triggers = $this->campaign->campaignTriggers->map(function ($trigger) {
                    return [
                        'id' => $trigger->id,
                        'type' => $trigger->type,
                        'value' => $trigger->value,
                        'operator' => $trigger->operator,
                    ];
                })->toArray();
            } else {
                $this->initializeEmptyTriggers();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Campaign not found or could not be loaded.');
            return redirect()->route('campaigns.index');
        }
    }

    protected function initializeEmptyForm()
    {
        $this->initializeEmptyWebsites();
        $this->initializeEmptyTriggers();
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

    protected function initializeEmptyTriggers()
    {
        $this->triggers = [
            [
                'type' => '',
                'value' => '',
                'operator' => '',
            ]
        ];
    }

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

    public function addTrigger()
    {
        $this->triggers[] = [
            'type' => '',
            'value' => '',
            'operator' => '',
        ];
    }

    public function removeTrigger($index)
    {
        if (count($this->triggers) > 1) {
            unset($this->triggers[$index]);
            $this->triggers = array_values($this->triggers);
        }
    }

    // Live validation methods
    public function updated($propertyName)
    {
        // Skip validation for certain properties to avoid issues
        if (str_contains($propertyName, '.')) {
            $this->validateOnly($propertyName);
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function submit()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('scrollToFirstError'); // Livewire 3 event
            throw $e; // Re-throw to show errors
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

            // Dispatch event to scroll to first error
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
        ]);

        $this->saveCampaignWebsites($campaign);
        $this->saveCampaignTriggers($campaign);
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
        ]);

        // Delete existing relationships and recreate them
        $this->campaign->campaignWebsites()->delete();
        $this->campaign->campaignTriggers()->delete();

        $this->saveCampaignWebsites($this->campaign);
        $this->saveCampaignTriggers($this->campaign);
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

    protected function saveCampaignTriggers($campaign)
    {
        foreach ($this->triggers as $trigger) {
            if (!empty($trigger['type']) && !empty($trigger['value']) && !empty($trigger['operator'])) {
                CampaignTrigger::create([
                    'campaign_id' => $campaign->id,
                    'type' => $trigger['type'],
                    'value' => $trigger['value'],
                    'operator' => $trigger['operator'],
                ]);
            }
        }
    }

    // Helper method to get current campaign for debugging
    public function getCurrentCampaign()
    {
        return $this->campaign;
    }

    public function render()
    {
        return view('livewire.admin.campaigns.campaign-manager');
    }
}
