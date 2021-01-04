<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiNotFoundException;
use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\ProductRequest;
use App\Libs\ApiResponse;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends BaseController
{
    /**
     * Return filterable paginated list of product.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $perPage = request()->query('perPage');
        $name = request()->query('name');
        return ApiResponse::make()->paginator(Product::filterByName($name)->paginate($perPage))->json();
    }

    /**
     * Store a newly created product in database.
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store(ProductRequest $request)
    {
        return ApiResponse::make()->isCreated(Product::uploadImageAndCreate($request->validated()))->json();
    }

    /**
     * Return the specified product.
     *
     * @param  int  $id
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function show($id)
    {
        return ApiResponse::make()->data(Product::findOrThrow($id))->json();
    }

    /**
     * Update the specified product
     * @param ProductRequest $request
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function update(ProductRequest $request)
    {
        $product = Product::findOrThrow(request('product'))->uploadImageAndUpdate($request->validated());
        return ApiResponse::make()->isUpdated($product)->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws ApiNotFoundException
     * @return JsonResponse
     */
    public function destroy()
    {
        $product = Product::findOrThrow(request('product'));

        $product->delete();

        return ApiResponse::make()->isDeleted()->json();
    }

}
