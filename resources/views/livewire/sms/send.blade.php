<?php

use Livewire\Volt\Component;
use App\Models\Sms;
use Illuminate\Support\Facades\Http;

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

        $phones = array_map('trim', explode(',', $this->phones));

        $response = Http::withBasicAuth($user->sms_gateway_username, $user->sms_gateway_password)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($user->sms_gateway_url, [
                'message' => $this->message,
                'phoneNumbers' => $phones,
            ]);
        
        foreach ($phones as $phone) {
            Sms::create([
                'user_id' => $user->id,
                'phone_number' => $phone,
                'message' => $this->message,
                'response' => $response->json(),
            ]);
        }

        $this->reset('phones', 'message');

        $this->dispatch('sms-sent');
    }
}; ?>

<section>
        <form wire:submit="sendSms" class="mt-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <flux:textarea
                    wire:model="phones"
                    :label="__('Phone numbers')"
                    required
                    badge="required"
                    rows="auto"
                    placeholder="07656776889, 0654456789 ..."
                />
                <flux:textarea
                    wire:model="message"
                    :label="__('Message')"
                    required
                    badge="required"
                    rows="auto"
                    placeholder="Hello, this is a test message"
                />
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Send sms') }}</flux:button>
                </div>
            </div>
        </form>
</section>
