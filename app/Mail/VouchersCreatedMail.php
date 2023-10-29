<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $vouchers;
    public User $user;
    public array $errors;

    public function __construct(array $vouchers, User $user, array $errors)
    {
        $this->vouchers = $vouchers;
        $this->user = $user;
        $this->errors = $errors;
    }

    public function build(): self
    {
        return $this->view('emails.comprobante')
            ->with(['comprobantes' => $this->vouchers, 'user' => $this->user, 'errors' => $this->errors]);
    }
}
