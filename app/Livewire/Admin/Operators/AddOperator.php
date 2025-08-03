<?php

namespace App\Livewire\Admin\Operators;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Operator;

#[Layout('layouts.app')]
class AddOperator extends Component
{
    #[Validate('required|string|max:255|unique:operators,name')]
    public $name = '';

    #[Validate('required|url|max:255|unique:operators,website_url')]
    public $website_url = '';

    #[Validate('nullable|url|max:255')]
    public $logo_url = '';

    public function save()
    {
        $this->validate();

        $operator = Operator::create([
            'name' => $this->name,
            'website_url' => $this->website_url,
            'logo_url' => $this->logo_url ?: null,
        ]);

        session()->flash('success', 'Operator created successfully.');

        $this->dispatch('operator-created');

        return redirect()->route('operators.index');
    }

    public function render()
    {
        return view('livewire.admin.operators.add-operator');
    }
}
