<?php

namespace App\Livewire\Admin\Websites;

use Livewire\Component;
use App\Models\Website;
use App\Enums\WebsiteTypeEnum;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Layout;

/**
 * Livewire component for adding a new website.
 */
#[Layout('layouts.app')]
class AddWebsite extends Component
{
    public string $url = '';
    public string $api_url = '';
    public int $type = WebsiteTypeEnum::UNKNOWN->value;

    public string $auth_type = 'NONE';
    public ?string $auth_token = null;
    public ?string $auth_user = null;
    public ?string $auth_pass = null;

    protected function rules(): array
    {
        return [
            'url'        => ['required|unique:websites,url', 'url'],
            'api_url'    => ['required|unique:websites,api_url', 'url'],
            'type'       => ['required', new Enum(WebsiteTypeEnum::class)],
            'auth_type'  => ['required', 'in:NONE,TOKEN,BASIC'],
            'auth_token' => ['required_if:auth_type,TOKEN', 'nullable', 'string'],
            'auth_user'  => ['required_if:auth_type,BASIC', 'nullable', 'string'],
            'auth_pass'  => ['required_if:auth_type,BASIC', 'nullable', 'string'],
        ];
    }

    protected array $messages = [
        'url.required'          => 'The website URL is required.',
        'url.url'               => 'Please enter a valid URL.',
        'url.unique'            => 'The website URL already exists.',
        'api_url.required'      => 'The API URL is required.',
        'api_url.url'           => 'Please enter a valid API URL.',
        'api_url.unique'        => 'The API URL already exists.',
        'type.required'         => 'Please select a website type.',
        'auth_token.required_if' => 'An auth token is required when the Token auth type is selected.',
        'auth_user.required_if'  => 'Auth user is required for Basic auth.',
        'auth_pass.required_if'  => 'Auth password is required for Basic auth.',
    ];

    public function save(): void
    {
        $this->validate();

        Website::create([
            'url'        => $this->url,
            'api_url'    => $this->api_url,
            'type'       => $this->type,
            'auth_type'  => $this->auth_type,
            'auth_token' => $this->auth_token,
            'auth_user'  => $this->auth_user,
            'auth_pass'  => $this->auth_pass,
        ]);

        session()->flash('message', __('Website added successfully.'));
        $this->redirectRoute('websites.index');
    }

    public function render()
    {
        return view('livewire.admin.websites.add-website', [
            'websiteTypes' => WebsiteTypeEnum::cases(),
        ]);
    }
}
