<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Product as ProductResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Return filterable paginated list of product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $perPage = request()->get('perPage');
        $name = request()->get('name');
        return ApiResponse::make()->paginator(Product::filterByName($name)->paginate($perPage))->json();
    }

    /**
     * Store a newly created product in database.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        $validator = Validator::make(request()->all(),$this->rule());

        if($validator->fails()){
            return ApiResponse::make()->isNotValid($validator->errors())->json();
        }

        //get validated product
        $data = $validator->validated();

        //upload & set image path on data if image exist
        $this->uploadIfImageExists($data);

        return ApiResponse::make()->data(Product::create($data))->isCreated()->json();
    }

    /**
     * Return the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::find($id);
        return $product ? ApiResponse::make()->data($product)->json()
                        : ApiResponse::make()->isNotFound()->json();
    }

    /**
     * Update the specified product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        //if product is not found
        if(! $product = Product::find(request('product'))){
            //Return early
            return ApiResponse::make()->isNotFound()->json();
        }

        $validator = Validator::make(request()->all(), $this->rule());
        if($validator->fails()){
            return ApiResponse::make()->data(request()->all())
                ->isNotValid($validator->errors())
                ->json();
        }

        //get validated product data
        $data = $validator->validated();

        //upload & set image path on product if image exist
        $this->uploadIfImageExists($data);

        $product->update($data);

        return ApiResponse::make()->data($product)->isUpdated()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $product = Product::find(request('product'));

        if(!$product) return ApiResponse::make()->isNotFound()->json();

        $product->delete();

        return ApiResponse::make()->isDeleted()->json();
    }

    private function rule(){
        return  [
            'id' => 'required',
            'barcode' => '',
            'name' => 'required',
            'name_initial' => '',
            'unit_id' => 'required|numeric|exists:units,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'stock_type' => 'required|string|in:single,composite',
            'primary_ingredient_id' => '',
            'primary_ingredient_qty' => '',
            'for_sale' => 'required|boolean',
            'image' => 'nullable|image',
            'minimum_qty' => '',
            'minimum_expiration_days' => ''
        ];
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
