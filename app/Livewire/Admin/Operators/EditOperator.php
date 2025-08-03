<?php

namespace App\Livewire\Admin\Operators;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\Operator;

#[Layout('layouts.app')]
class EditOperator extends Component
{
    public Operator $operator;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|url|max:255')]
    public $website_url = '';

    #[Validate('nullable|url|max:255')]
    public $logo_url = '';

    public function mount(Operator $operator)
    {
        $this->operator = $operator;
        $this->name = $operator->name;
        $this->website_url = $operator->website_url;
        $this->logo_url = $operator->logo_url;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:operators,name,' . $this->operator->id,
            'website_url' => 'required|url|max:255|unique:operators,website_url,' . $this->operator->id,
            'logo_url' => 'nullable|url|max:255',
        ];
    }

    public function update()
    {
        $this->validate();

        $this->operator->update([
            'name' => $this->name,
            'website_url' => $this->website_url,
            'logo_url' => $this->logo_url ?: null,
        ]);

        session()->flash('success', 'Operator updated successfully.');

        $this->dispatch('operator-updated');

        return redirect()->route('operators.index');
    }

    public function render()
    {
        return view('livewire.admin.operators.edit-operator');
    }
}
