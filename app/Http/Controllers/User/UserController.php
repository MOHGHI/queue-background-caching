<?php

namespace App\Http\Controllers\User;

use App\Events\UserVisit;
use App\Http\Resources\CPanel\UsersResource;
use App\Jobs\AddView;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use DB;

class UserController extends Controller
{
    use  ResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(User $model)
    {

        $users = $model
            ->select("*",
                \DB::raw('(SELECT Count(id) FROM visits WHERE visits.user_id = users.id) as visits'))
            ->orderBy('visits', 'DESC')
            ->paginate(PAGINATION_COUNT);

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
}
