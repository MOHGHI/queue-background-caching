<?php

namespace App\Http\Controllers\User;

use App\Events\UserVisit;
use App\Http\Resources\CPanel\UsersResource;
use App\Jobs\AddView;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;

class UserController extends Controller
{
    use  ResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(User $model)
    {
        $users = $model->paginate(PAGINATION_COUNT);
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
    public function show($userId)
    {
        $user = User::select('id', 'name')->find($userId);
        event(new UserVisit($user));
        return $this->returnData('user', $user);
    }
}
