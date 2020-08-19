<?php

namespace App\Listeners;

use App\Events\UserVisit;
use App\Models\Visit;
use DB;
class IncrementUserViews
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserVisit $event)
    {
        visit::create(['user_id' => $event -> user -> id ]);  // add or duplicate new visit
    }
}
