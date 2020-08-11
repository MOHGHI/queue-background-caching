<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\View;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddView implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = $this->users;
        if (isset($users) && count($users) > 0) {
            foreach ($users as $user) {
                View::updateOrCreate(
                    ['user_id' => $users->id],
                    ['count' => DB::raw('count + 1')]
                );

            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    private function addUserViews($id)
    {
        View::create(['user_id' => $id, 'count' => 1]);
    }

}
