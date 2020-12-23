<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiNotFoundException;
use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\UserRequest;
use App\Libs\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsersController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $perPage = request()->get('perPage');
        $name = request()->get('name');
        return ApiResponse::make()->paginator(User::filterByName($name)->paginate($perPage))->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request)
    {
        return ApiResponse::make()->isCreated(User::UploadImageAndCreate($request->validated()))->json();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @throws ApiNotFoundException
     * @return JsonResponse
     */
    public function show($id)
    {
        return ApiResponse::make()->data(User::tryToFind($id))->json();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     * @throws ApiNotFoundException
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::tryToFind($id);
        $user->uploadImageAndUpdate($request->validated());
        return ApiResponse::make()->isUpdated($user)->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function destroy($id)
    {
        $user = User::tryToFind($id);

        $user->delete();

        return ApiResponse::make()->isDeleted()->json();
    }

}
