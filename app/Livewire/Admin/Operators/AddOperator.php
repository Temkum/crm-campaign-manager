<?php

namespace App\Livewire\Admin\Operators;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Operator;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class AddOperator extends Component
{
    public $name = '';
    public $website_url = '';
    public $logo_url = '';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('operators', 'name')
            ],
            'website_url' => [
                'required',
                'url',
                'max:255',
                Rule::unique('operators', 'website_url')
            ],
            'logo_url' => [
                'nullable',
                'url',
                'max:255'
            ]
        ];
    }

    protected function messages()
    {
        return [
            'name.unique' => 'An operator with this name already exists.',
            'website_url.url' => 'Please enter a valid website URL.',
        ];
    }

    public function save()
    {
        $this->validate();
        Operator::create([
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
