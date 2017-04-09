<?php

namespace App\Console\Commands;

use Mail;
use App\Round;
use Illuminate\Console\Command;
use App\Mail\CoachRoundReminderEmail;

class SendCoachesRoundReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:roundReminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a reminder email to all coaches who have not yet filled in rounds, where the default_date of the Round has passed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Round::all()->reject(function ($round) {
            // reject all rounds where the default_date has not passed
            return $round->default_date->gt(\Carbon\Carbon::now());
        })->each(function ($round) {
            \App\Team::all()->reject(function ($team) use ($round) {
                // reject all teams that have current round filled in or that don't have a coach
                return $team->rounds()->find($round->id) || ! $team->hasCoach();
            })->each(function ($team) use ($round) {
                // send an email to the remaining coaches
                Mail::to($team->user)->queue(new CoachRoundReminderEmail($team, $round));
                $this->info('Reminder sent!');
            });
        });
    }
}
