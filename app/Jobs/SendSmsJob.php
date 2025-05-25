<?php
namespace App\Jobs;

use App\Models\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;
    protected $phoneNumbers;

    public function __construct($user, $message, $phoneNumbers)
    {
        $this->user = $user;
        $this->message = $message;
        $this->phoneNumbers = $phoneNumbers;
    }

    public function handle(): void
    {
        $response = Http::withBasicAuth($this->user->sms_gateway_username, $this->user->sms_gateway_password)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($this->user->sms_gateway_url, [
                'message' => $this->message,
                'phoneNumbers' => $this->phoneNumbers,
            ]);

        Sms::create([
            'user_id' => $this->user->id,
            'phone_number' => json_encode($this->phoneNumbers),
            'message' => $this->message,
            'response' => json_encode($response->json()),
        ]);

    }
}
