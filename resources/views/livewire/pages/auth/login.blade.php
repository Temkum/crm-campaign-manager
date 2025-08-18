<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
  <div class="text-center">
    <h2 class="text-2xl mb-4 font-bold text-gray-900 dark:text-white">Welcome back</h2>
  </div>
  {{-- session status --}}
  <x-auth-session-status class="mb-6" :status="session('status')" />

  {{-- email login --}}
  <div class="space-y-3 mb-6">
    <button type="button"
      class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900">
      <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
        <path fill="currentColor"
          d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
        <path fill="currentColor"
          d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
        <path fill="currentColor"
          d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
      </svg>
      Continue with Google
    </button>
  </div>

  {{-- divider --}}
  <div class="relative mb-6">
    <div class="absolute inset-0 flex items-center">
      <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
    </div>
    <div class="relative flex justify-center text-sm">
      <span class="px-2 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400">Or continue with email</span>
    </div>
  </div>

  <form wire:submit="login" class="space-y-6">
    {{-- email --}}
    <div>
      <x-input-label for="email" :value="__('Email address')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
      <div class="mt-2 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
            </path>
          </svg>
        </div>
        <x-text-input wire:model="form.email" id="email"
          class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-gray-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors duration-200"
          type="email" name="email" placeholder="Enter your email" required autofocus autocomplete="username" />
      </div>
      <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div>
      <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700 dark:text-gray-300" />
      <div class="mt-2 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 15v2m-6 4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM12 9a3 3 0 100-6 3 3 0 000 6z">
            </path>
          </svg>
        </div>
        <x-text-input wire:model="form.password" id="password"
          class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-gray-700 dark:focus:ring-blue-400 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors duration-200"
          type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
      </div>
      <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
    </div>

    {{-- Remember Me and Forgot Password --}}
    <div class="flex items-center justify-between">
      <div class="flex items-center">
        <input wire:model="form.remember" id="remember" name="remember" type="checkbox"
          class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-800 transition-colors duration-200" />
        <label for="remember" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
          {{ __('Remember me') }}
        </label>
      </div>

      @if (Route::has('password.request'))
        <a class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900 rounded-md"
          href="{{ route('password.request') }}" wire:navigate>
          {{ __('Forgot password?') }}
        </a>
      @endif
    </div>

    {{-- submit --}}
    <div>
      <x-primary-button
        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900 transition-all duration-200 transform hover:scale-[1.02]">
        <svg wire:loading wire:target="login" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
          </circle>
          <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
          </path>
        </svg>
        <span wire:loading.remove wire:target="login">{{ __('Sign in to your account') }}</span>
        <span wire:loading wire:target="login">{{ __('Signing in...') }}</span>
      </x-primary-button>
    </div>
  </form>

  <!-- Bottom Links -->
  <div class="mt-6 text-center">
    <p class="text-sm text-gray-600 dark:text-gray-400">
      Don't have an account?
      <a href="{{ route('register') }}" wire:navigate
        class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
        Create an account
      </a>
    </p>
  </div>
</div>
