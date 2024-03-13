<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Models\User\UserDeleted;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SendDestroyUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserDeleted  $event
     * @return void
     */
    public function handle($event): void
    {
        Mail::to($event->user)
            ->send(new WelcomeMail($event->user));
    }
}
