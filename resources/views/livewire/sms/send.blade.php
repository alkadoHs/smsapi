<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $phones = '';
    public string $message = '';

    public function sendSms(): void
    {
        $this->validate([
            'phones' => 'required|string',
            'message' => 'required|string',
        ]);

        $user = auth()->user();

        $response = Http::withBasicAuth($user->sms_gateway_username, $user->sms_gateway_password)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($user->sms_gateway_url, [
                'message' => $this->message,
                'phoneNumbers' => array_map('trim', explode(',', $this->phones)),
            ]);

        $this->reset('phones', 'message');

        $this->dispatch('sms-sent');
    }
}; ?>

<section>
   
        <form wire:submit="sendSms" class="mt-6 space-y-6">
            <flux:input
                wire:model="phones"
                :label="__('Phone numbers')"
                type="text"
                required
                placeholder="07656776889, 0654456789 ..."
            />
            <flux:input
                wire:model="message"
                :label="__('Message')"
                type="textarea"
                required
                placeholder="Hello, this is a test message"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
</section>
