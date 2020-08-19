<?php

namespace App\Http\Controllers\User;

use App\Events\UserVisit;
use App\Http\Resources\CPanel\UsersResource;
use App\Jobs\AddView;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use DB;

class UserController extends Controller
{
    use  ResponseTrait;

    const CACHE_KEY = 'USERS';

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(User $model)
    {

        $key = "allUsers.counters";
        $cacheKey = $this->getCacheKey($key);

        return $users = cache()->remember($cacheKey, Carbon::now()->addMinute(CAHE_PERIOD_MINUTES), function () use ($model) {

            return $model->leftJoin('userviews', 'users.id', '=', 'userviews.user_id')
                ->orderBy('userviews.counter', 'DESC')
                ->select("id",
                    "name",
                    "email",
                    DB::Raw('IFNULL(counter , 0 ) as counter')
                )
                ->paginate(10);

        });


        dispatch(new AddView($users))->onQueue('new_views');   // run job in background for load/time consume
        $users = new UsersResource($users);
        return $this->returnData('users', $users);
    }

    /**
     * Show the specified resource.
     *
     * @param integer $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function visit($userId)
    {
        $user = User::select('id', 'name')->find($userId);
        event(new UserVisit($user));
        return $this->returnData('user', $user);
    }

    public function getCacheKey($key)
    {
        $key = strtoupper($key);
        return self::CACHE_KEY . ".$key";
    }
}
