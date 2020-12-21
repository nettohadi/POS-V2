<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiNotFoundException;
use App\Http\Controllers\Base\BaseController;
use App\Http\Requests\ProductRequest;
use App\Libs\ApiResponse;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductsController extends BaseController
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
        //get validated product
        $data = $request->validated();

        //upload & set image path on data if image exist
        $this->uploadIfImageExists($data);

        return ApiResponse::make()->data(Product::create($data))->isCreated()->json();
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
        return ApiResponse::make()->data(Product::tryToFind($id))->json();
    }

    /**
     * Update the specified product
     * @param ProductRequest $request
     * @return JsonResponse
     * @throws ApiNotFoundException
     */
    public function update(ProductRequest $request)
    {
        $product = Product::tryToFind(request('product'));

        //get validated product data
        $data = $request->validated();

        //upload & set image path on product if image exist
        $this->uploadIfImageExists($data);

        $product->update($data);

        return ApiResponse::make()->data($product)->isUpdated()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws ApiNotFoundException
     * @return JsonResponse
     */
    public function destroy()
    {
        $product = Product::tryToFind(request('product'));

        $product->delete();

        return ApiResponse::make()->isDeleted()->json();
    }


    private function uploadIfImageExists(array &$data)
    {
        if(!$data) return;

        if((request()->file('image'))){
            $imageFile = request()->file('image');
            $path = "/products";
            $newFileName = Product::generateImageName('product',$imageFile->getClientOriginalExtension());

            $isUploaded = Storage::disk('public')->putFileAs($path, $imageFile, $newFileName);
            $data['image'] = $isUploaded ? $path.'/'.$newFileName : null;
        }else{
            //if image is nul, remove image
            unset($data['image']);
        }
    }
}
