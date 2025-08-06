<?php

namespace App\Livewire\Admin\Campaigns;

use Livewire\Component;

class TriggerGroupForm extends Component
{
    public array $group = [];
    public int $groupIndex;
    public array $triggerTypes = [];
    public array $triggerOperators = [];
    public bool $canRemoveGroup = false;
    public bool $canMoveUp = false;
    public bool $canMoveDown = false;

    public function mount($group, $groupIndex, $triggerTypes, $triggerOperators, $canRemoveGroup = false, $canMoveUp = false, $canMoveDown = false)
    {
        $this->group = $group;
        $this->groupIndex = $groupIndex;
        $this->triggerTypes = $triggerTypes;
        $this->triggerOperators = $triggerOperators;
        $this->canRemoveGroup = $canRemoveGroup;
        $this->canMoveUp = $canMoveUp;
        $this->canMoveDown = $canMoveDown;
    }

    public function addTrigger()
    {
        $maxOrderIndex = 0;
        foreach ($this->group['triggers'] as $trigger) {
            if ($trigger['order_index'] > $maxOrderIndex) {
                $maxOrderIndex = $trigger['order_index'];
            }
        }

        $this->group['triggers'][] = [
            'id' => null,
            'type' => '',
            'operator' => '',
            'value' => '',
            'description' => '',
            'order_index' => $maxOrderIndex + 1,
        ];

        $this->dispatch('groupUpdated', $this->groupIndex, $this->group);
    }

    public function removeTrigger($triggerIndex)
    {
        if (count($this->group['triggers']) > 1) {
            unset($this->group['triggers'][$triggerIndex]);
            $this->group['triggers'] = array_values($this->group['triggers']);

            // Reorder remaining triggers
            foreach ($this->group['triggers'] as $index => &$trigger) {
                $trigger['order_index'] = $index;
            }

            $this->dispatch('groupUpdated', $this->groupIndex, $this->group);
        }
    }

    public function moveTriggerUp($triggerIndex)
    {
        if ($triggerIndex > 0) {
            $temp = $this->group['triggers'][$triggerIndex];
            $this->group['triggers'][$triggerIndex] = $this->group['triggers'][$triggerIndex - 1];
            $this->group['triggers'][$triggerIndex - 1] = $temp;

            // Update order indices
            $this->group['triggers'][$triggerIndex]['order_index'] = $triggerIndex;
            $this->group['triggers'][$triggerIndex - 1]['order_index'] = $triggerIndex - 1;

            $this->dispatch('groupUpdated', $this->groupIndex, $this->group);
        }
    }

    public function moveTriggerDown($triggerIndex)
    {
        if ($triggerIndex < count($this->group['triggers']) - 1) {
            $temp = $this->group['triggers'][$triggerIndex];
            $this->group['triggers'][$triggerIndex] = $this->group['triggers'][$triggerIndex + 1];
            $this->group['triggers'][$triggerIndex + 1] = $temp;

            // Update order indices
            $this->group['triggers'][$triggerIndex]['order_index'] = $triggerIndex;
            $this->group['triggers'][$triggerIndex + 1]['order_index'] = $triggerIndex + 1;

            $this->dispatch('groupUpdated', $this->groupIndex, $this->group);
        }
    }

    public function updatedGroup()
    {
        $this->dispatch('groupUpdated', $this->groupIndex, $this->group);
    }

    public function moveGroupUp()
    {
        $this->dispatch('moveGroupUp', $this->groupIndex);
    }

    public function moveGroupDown()
    {
        $this->dispatch('moveGroupDown', $this->groupIndex);
    }

    public function removeGroup()
    {
        $this->dispatch('removeGroup', $this->groupIndex);
    }

    public function render()
    {
        return view('livewire.admin.campaigns.trigger-group-form');
    }
}
