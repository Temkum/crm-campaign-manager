<?php

namespace App\Livewire\Admin\Markets;

use App\Models\Market;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AddMarket extends Component
{
    #[Validate('required|string|max:255|unique:markets,name')]
    public $name = '';

    #[Validate('required|string|size:2|unique:markets,iso_code')]
    public $iso_code = '';

    public function save()
    {
        $this->validate();

        $market = Market::create([
            'name' => $this->name,
            'iso_code' => strtoupper($this->iso_code),
        ]);

        session()->flash('success', 'Market created successfully.');

        $this->dispatch('market-created');

        return redirect()->route('markets.index');
    }

    public function render()
    {
        return view('livewire.admin.markets.add-market');
    }
}
