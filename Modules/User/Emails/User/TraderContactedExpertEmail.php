<?php

namespace Modules\User\Emails\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TraderContactedExpertEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var user
     */
    public $trader;

    /**
     * @var user
     */
    public $expert;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($trader, $expert)
    {
        $this->trader = $trader;
        $this->expert = $expert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('user::emails.users.trader-contacted-expert', [
          'trader' => $this->trader,
          'expert' => $this->expert
          ])->subject(trans('user::email.subject.trader-contacted-expert'));
    }
}
