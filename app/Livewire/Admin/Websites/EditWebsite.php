<?php

namespace App\Livewire\Admin\Websites;

use Livewire\Component;
use App\Models\Website;
use App\Enums\WebsiteTypeEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class EditWebsite extends Component
{
    use AuthorizesRequests;

    public Website $website;

    #[Validate('required|url|max:255|string')]
    public string $url = '';

    #[Validate('nullable|url|max:255|string')]
    public string $api_url = '';

    #[Validate('required')]
    public string $type = '';

    #[Validate('required|in:NONE,TOKEN,BASIC')]
    public string $auth_type = 'NONE';

    public ?string $auth_token = null;
    public ?string $auth_user = null;
    public ?string $auth_pass = null;

    // Track original encrypted values to determine if they should be updated
    private ?string $original_auth_token = null;
    private ?string $original_auth_pass = null;

    public function mount(Website $website): void
    {
        // $this->authorize('update', $website);

        // Store original encrypted values
        $this->original_auth_token = $website->auth_token;
        $this->original_auth_pass = $website->auth_pass;
        $this->url = $website->url;
        $this->api_url = $website->api_url ?? '';
        $this->type = $website->type?->value ?? WebsiteTypeEnum::UNKNOWN->value;
        $this->auth_type = $website->auth_type;
        $this->auth_user = $website->auth_user;
        $this->auth_pass = $website->auth_pass;
        $this->auth_token = $website->auth_token;
    }

    protected function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                'string',
                'max:255',
                Rule::unique('websites', 'url')->ignore($this->website->id),
            ],
            'api_url' => ['nullable', 'url', 'max:255', 'string'],
            'type' => ['required', new Enum(WebsiteTypeEnum::class)],
            'auth_type' => ['required', 'in:NONE,TOKEN,BASIC'],
            'auth_token' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($this->auth_type === 'TOKEN' && empty($value) && empty($this->original_auth_token)) {
                        $fail('An auth token is required when Token authentication is selected.');
                    }
                },
            ],
            'auth_user' => [
                'required_if:auth_type,BASIC',
                'nullable',
                'string',
                'max:255',
            ],
            'auth_pass' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($this->auth_type === 'BASIC' && empty($value) && empty($this->original_auth_pass)) {
                        $fail('A password is required when Basic authentication is selected.');
                    }
                },
            ],
        ];
    }

    protected array $messages = [
        'url.required' => 'The website URL is required.',
        'url.url' => 'Please enter a valid URL (e.g., https://example.com).',
        'url.unique' => 'This website URL is already registered.',
        'url.max' => 'The website URL must not exceed 255 characters.',
        'api_url.url' => 'Please enter a valid API URL (e.g., https://api.example.com).',
        'api_url.max' => 'The API URL must not exceed 255 characters.',
        'type.required' => 'Please select a website type.',
        'auth_user.required_if' => 'Username is required for Basic authentication.',
        'auth_user.max' => 'Username must not exceed 255 characters.',
        'auth_token.max' => 'Auth token must not exceed 255 characters.',
        'auth_pass.max' => 'Password must not exceed 255 characters.',
    ];

    public function updatedAuthType(): void
    {
        // Clear auth fields when auth type changes
        if ($this->auth_type === 'NONE') {
            $this->reset(['auth_token', 'auth_user', 'auth_pass']);
        } elseif ($this->auth_type === 'TOKEN') {
            $this->reset(['auth_user', 'auth_pass']);
        } elseif ($this->auth_type === 'BASIC') {
            $this->reset(['auth_token']);
        }

        // Re-validate after clearing fields
        $this->resetValidation();
    }

    public function update(): void
    {
        // $this->authorize('update', $this->website);

        try {
            $validated = $this->validate();

            // Prepare update data
            $updateData = [
                'url' => $validated['url'],
                'api_url' => $validated['api_url'],
                'type' => WebsiteTypeEnum::from($validated['type']),
                'auth_type' => $validated['auth_type'],
            ];

            // Handle authentication fields based on type
            switch ($validated['auth_type']) {
                case 'NONE':
                    $updateData = array_merge($updateData, [
                        'auth_token' => null,
                        'auth_user' => null,
                        'auth_pass' => null,
                    ]);
                    break;

                case 'TOKEN':
                    $updateData = array_merge($updateData, [
                        'auth_user' => null,
                        'auth_pass' => null,
                    ]);

                    // Only update token if a new one is provided
                    if (!empty($validated['auth_token'])) {
                        $updateData['auth_token'] = Crypt::encryptString($validated['auth_token']);
                    }
                    break;

                case 'BASIC':
                    $updateData['auth_token'] = null;
                    $updateData['auth_user'] = $validated['auth_user'];

                    // Only update password if a new one is provided
                    if (!empty($validated['auth_pass'])) {
                        $updateData['auth_pass'] = Crypt::encryptString($validated['auth_pass']);
                    }
                    break;
            }

            $this->website->update($updateData);

            session()->flash('message', __('Website updated successfully.'));

            $this->redirectRoute('websites.index', navigate: true);
        } catch (\Exception $e) {
            Log::error('Failed to update website: ' . $e->getMessage(), [
                'website_id' => $this->website->id,
            ]);

            session()->flash('error', __('Failed to update website. Please try again.'));
        }
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->website);

        try {
            $websiteName = $this->website->url;
            $this->website->delete();

            session()->flash('message', __('Website ":name" has been deleted successfully.', ['name' => $websiteName]));

            $this->redirectRoute('websites.index', navigate: true);
        } catch (\Exception $e) {
            Log::error('Failed to delete website: ' . $e->getMessage(), [
                'website_id' => $this->website->id,
            ]);

            session()->flash('error', __('Failed to delete website. Please try again.'));
        }
    }

    /**
     * Real-time URL validation
     */
    public function updatedUrl(): void
    {
        $this->validateOnly('url');
    }

    /**
     * Real-time API URL validation
     */
    public function updatedApiUrl(): void
    {
        if (!empty($this->api_url)) {
            $this->validateOnly('api_url');
        }
    }

    public function render()
    {
        return view('livewire.admin.websites.edit-website', [
            'websiteTypes' => WebsiteTypeEnum::cases(),
        ]);
    }
}
