<?php

namespace app\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Attachment;

use app\Models\Client;

class LetterOfHappiness extends Mailable
{
    use Queueable, SerializesModels;

    private $client;
    private $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Client $client, $filePath)
    {
        $this->client = $client;
        $this->filePath = $filePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Letter Of Happiness',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.letter_of_happiness',
            with: [
                "client" => $this->client
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path($this->filePath))->as("Letter_of_Happiness.pdf")
        ];
    }
}
