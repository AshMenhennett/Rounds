<?php

namespace App\Mail;

use App\Team;
use App\Round;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CoachRoundReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $team;
    public $round;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Team $team, Round $round)
    {
        $this->team = $team;
        $this->round = $round;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.coachReminder');
    }
}
