<?php

namespace App\Http\Controllers;

use App\Libs\MyResponse;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Product as ProductResource;
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
        return MyResponse::make()->paginator(Product::filterByName($name)->paginate($perPage))->json();
    }

    /**
     * Return a single product which match the id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        //
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
            return MyResponse::make()->isNotValid($validator->errors())->json();
        }

        return MyResponse::make()->data(Product::create($validator->validated()))->isCreated()->json();
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
        return $product ? MyResponse::make()->data($product)->json()
                        : MyResponse::make()->isNotFound()->json();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function rule(){
        return  [
            'id' => 'required',
            'barcode' => '',
            'name' => 'required',
            'name_initial' => '',
            'unit_id' => 'required',
            'category_id' => 'required',
            'stock_type' => 'required',
            'primary_ingredient_id' => '',
            'primary_ingredient_qty' => '',
            'for_sale' => 'required',
            'image' => '',
            'minimum_qty' => '',
            'minimum_expiration_days' => ''
        ];
    }
}
