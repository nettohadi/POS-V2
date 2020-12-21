<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Base\BaseController;

class OutletsController extends BaseController
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
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store()
    {
        $validator = Validator::make(request()->all(), $this->rule());

        if($validator->fails()){
            return ApiResponse::make()->isNotValid($validator->errors())->json();
        }

        //get validated request
        $data = $validator->validated();

        return ApiResponse::make()->data(Outlet::create($data))->isCreated()->json();
    }

    private function rule(){
        return [
            'id'             => 'required|string|alpha|max:5',
            'name'           =>'required|string|max:30',
            'address'        =>'required|string|max:100',
            'vat_percentage' => 'nullable|integer|numeric|min:0|max:100',
            'dp_percentage'  => 'nullable|integer|numeric|min:0|max:100',
            'social_media'   => 'nullable|string',
            'contact'        => 'nullable|string'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $outlet = Outlet::find($id);

        if(!$outlet) return ApiResponse::make()->isNotFound()->json();

        return ApiResponse::make()->data($outlet)->json();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return JsonResponse
     */
    public function update()
    {
        $outlet = Outlet::find(request('outlet'));

        if(!$outlet) return ApiResponse::make()->isNotFound()->json();

        $validator = Validator::make(request()->all(), $this->rule());

        if($validator->fails()){
            return ApiResponse::make()->isNotValid($validator->errors())->json();
        }

        $outlet->update($validator->validated());

        return ApiResponse::make()->data($outlet)->isUpdated()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $outlet = Outlet::find($id);

        if(!$outlet) return ApiResponse::make()->isNotFound()->json();

        $outlet->delete();

        return ApiResponse::make()->isDeleted()->json();
    }
}
