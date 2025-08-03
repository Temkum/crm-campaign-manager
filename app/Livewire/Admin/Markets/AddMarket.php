<?php

namespace App\Livewire\Admin\Markets;

use App\Models\Market;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class AddMarket extends Component
{
    public $name = '';
    public $iso_code = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:markets,name',
            'iso_code' => 'required|string|size:2|unique:markets,iso_code',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'A market with this name already exists.',
            'iso_code.unique' => 'A market with this ISO code already exists.',
        ];
    }

    public function save()
    {
        $this->validate($this->rules(), $this->messages());

        Market::create([
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
