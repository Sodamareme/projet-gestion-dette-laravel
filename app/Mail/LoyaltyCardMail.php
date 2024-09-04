<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $qr_code_url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client, $qr_code_url)
    {
        $this->client = $client;
        $this->qr_code_url = $qr_code_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.loyalty_card')
                    ->with([
                        'clientName' => $this->client->nom,
                        'clientPhone' => $this->client->telephone,
                    ])
                    ->attachFromStorageDisk('public', 'qrcodes/' . $this->client->user_id . '.png', 'loyalty_card.png');
    }
}
