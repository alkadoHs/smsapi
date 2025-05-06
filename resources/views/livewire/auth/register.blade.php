<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $sms_gateway_username = '';
    public string $sms_gateway_password = '';
    public string $sms_gateway_url = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'sms_gateway_username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'sms_gateway_password' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'sms_gateway_url' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        event(new Registered($user));

        $token = $user->createToken('sms-token')->plainTextToken;

        Auth::login($user);

        // Store the token in the session
        session()->put('sms_token', $token);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <!-- sms_gateway_username -->
        <flux:input
            wire:model="sms_gateway_username"
            :label="__('SMS Gateway Username')"
            type="text"
            required
            autocomplete="sms_gateway_username"
            :placeholder="__('SMS Gateway Username')"
        />

        <!-- sms_gateway_password -->
        <flux:input
            wire:model="sms_gateway_password"
            :label="__('SMS Gateway Password')"
            type="password"
            required
            autocomplete="sms_gateway_password"
            :placeholder="__('SMS Gateway Password')"
        />

        <!-- sms_gateway_url -->
        <flux:input
            wire:model="sms_gateway_url"
            :label="__('SMS Gateway URL')"
            type="text"
            required
            autocomplete="sms_gateway_url"
            :placeholder="__('SMS Gateway URL')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
