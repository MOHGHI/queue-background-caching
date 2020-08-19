<?php

use App\Models\User;
use App\Models\View;
use Illuminate\Database\Seeder;

class ViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            $user->views()->saveMany(
                factory(View::class, 5)->make(['user_id' => $user->id])
            );
        });
    }
}
