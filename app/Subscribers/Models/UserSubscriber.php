<?php


namespace App\Subscribers\Models;


use App\Events\Models\User\UserCreated;
use App\Events\Models\User\UserUpdated;
use App\Events\Models\User\UserDeleted;

use App\Listeners\SendWelcomeEmail;
use App\Listeners\SendUpdateEmail;
use App\Listeners\SendDestroyUser;


use Illuminate\Events\Dispatcher;

class UserSubscriber
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(UserCreated::class, SendWelcomeEmail::class);
        $events->listen(UserUpdated::class, SendUpdateEmail::class);
        $events->listen(UserDeleted::class, SendDestroyUser::class);

    }
}