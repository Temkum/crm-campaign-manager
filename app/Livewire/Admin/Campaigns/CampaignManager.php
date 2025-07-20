<?php

namespace App\Livewire\Admin\Campaigns;

use Livewire\Component;
use App\Models\Campaign;
use App\Models\Operator;
use App\Models\Market;
use App\Models\Website;
use App\Models\CampaignWebsite;
use App\Models\CampaignTrigger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

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
    public $status = 'draft';
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
    
    // UI state
    public $isEdit = false;

    protected function rules()
    {
        return [
            // Campaign core validation
            'name' => 'required|string|max:255',
            'operator_id' => 'required|exists:operators,id',
            'market_id' => 'required|exists:markets,id',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after:start_at',
            'status' => ['required', Rule::in(['draft', 'active', 'paused', 'completed'])],
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

    public function mount($campaignId = null)
    {
        $this->loadDropdownData();
        
        if ($campaignId) {
            $this->isEdit = true;
            $this->loadCampaign($campaignId);
        } else {
            $this->initializeEmptyForm();
        }
    }

    protected function loadDropdownData()
    {
        $this->operators = Operator::select('id', 'name')->get()->toArray();
        $this->markets = Market::select('id', 'name')->get()->toArray();
        $this->availableWebsites = Website::select('id', 'url', 'type')->get()->toArray();
    }

    protected function loadCampaign($campaignId)
    {
        $this->campaign = Campaign::with(['campaignWebsites.website', 'campaignTriggers'])
            ->findOrFail($campaignId);
        
        // Load campaign core fields
        $this->name = $this->campaign->name;
        $this->operator_id = $this->campaign->operator_id;
        $this->market_id = $this->campaign->market_id;
        $this->start_at = $this->campaign->start_at?->format('Y-m-d\TH:i');
        $this->end_at = $this->campaign->end_at?->format('Y-m-d\TH:i');
        $this->status = $this->campaign->status;
        $this->priority = $this->campaign->priority;
        $this->duration = $this->campaign->duration;
        $this->rotation_delay = $this->campaign->rotation_delay;
        $this->dom_selector = $this->campaign->dom_selector;
        
        // Load websites
        $this->websites = $this->campaign->campaignWebsites->map(function ($website) {
            return [
                'id' => $website->id,
                'website_id' => $website->website_id,
                'priority' => $website->priority,
                'dom_selector' => $website->dom_selector,
                'custom_affiliate_url' => $website->custom_affiliate_url,
                'timer_offset' => $website->timer_offset,
            ];
        })->toArray();
        
        // Load triggers
        $this->triggers = $this->campaign->campaignTriggers->map(function ($trigger) {
            return [
                'id' => $trigger->id,
                'type' => $trigger->type,
                'value' => $trigger->value,
                'operator' => $trigger->operator,
            ];
        })->toArray();
    }

    protected function initializeEmptyForm()
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
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

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

    protected function saveCampaignTriggers($campaign)
    {
        foreach ($this->triggers as $trigger) {
            CampaignTrigger::create([
                'campaign_id' => $campaign->id,
                'type' => $trigger['type'],
                'value' => $trigger['value'],
                'operator' => $trigger['operator'],
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.admin.campaigns.campaign-manager');
    }
}