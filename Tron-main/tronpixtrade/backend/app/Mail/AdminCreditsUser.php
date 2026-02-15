<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminCreditsUser extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Transaction $transaction;
    public string $amount;
    public string $accountType;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Transaction $transaction, string $accountType)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->amount = number_format($transaction->amount, 2);
        $this->accountType = ucfirst($accountType);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->accountType} Account Has Been Credited - \${$this->amount}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-credits-user',
            with: [
                'user' => $this->user,
                'transaction' => $this->transaction,
                'amount' => $this->amount,
                'accountType' => $this->accountType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
