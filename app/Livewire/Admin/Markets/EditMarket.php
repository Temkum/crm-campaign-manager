<?php

namespace App\Livewire\Admin\Markets;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Market;

#[Layout('layouts.app')]
class EditMarket extends Component
{
    public Market $market;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string|size:2')]
    public $iso_code = '';

    public function mount(Market $market)
    {
        $this->market = $market;
        $this->name = $market->name;
        $this->iso_code = $market->iso_code;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:markets,name,' . $this->market->id,
            'iso_code' => 'required|string|size:2|unique:markets,iso_code,' . $this->market->id,
        ];
    }

    public function update()
    {
        $this->validate();

        $this->market->update([
            'name' => $this->name,
            'iso_code' => strtoupper($this->iso_code),
        ]);

        session()->flash('success', 'Market updated successfully.');

        $this->dispatch('market-updated');

        return redirect()->route('markets.index');
    }

    public function render()
    {
        return view('livewire.admin.markets.edit-market');
    }
}
