<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiNotFoundException;
use App\Http\Requests\OutletRequest;
use App\Libs\ApiResponse;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Base\BaseController;

class OutletController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $perPage = request()->get('perPage');
        $name = request()->get('name');
        return ApiResponse::make()->paginator(Outlet::filterByName($name)->paginate($perPage))->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OutletRequest $request
     * @return JsonResponse
     */
    public function store(OutletRequest $request)
    {
        return ApiResponse::make()->data(Outlet::create($request->validated()))->isCreated()->json();
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function show($id)
    {
        return ApiResponse::make()->data(Outlet::findOrThrow($id))->json();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OutletRequest $request
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function update(OutletRequest $request)
    {
        $outlet = tap(Outlet::findOrThrow(request('outlet')))->update($request->validated());
        return ApiResponse::make()->isUpdated($outlet)->json();
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
        return ApiResponse::make()->isDeleted(tap(Outlet::findOrThrow($id))->delete())->json();
    }
}
