<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        $name    = request()->get('name') ?? null;
        $perPage = request()->get('perPage') ?? 10;

        $category = Category::filterByName($name)->with('type')->paginate($perPage);
        return ApiResponse::make()->paginator($category)->json();
    }

    public function show()
    {
        $category = Category::with('type')->find(request('category'));
        return $category
            ? ApiResponse::make()->data($category)->json()
            : ApiResponse::make()->isNotFound()->json();
    }

    public function store()
    {
        $validator = Validator::make(request()->all(),$this->rule());

        if($validator->fails()){
            return ApiResponse::make()->isNotValid($validator->errors())->json();
        }

        $category = Category::create($validator->validated());
        $category = Category::find($category->id);

        return ApiResponse::make()->data($category)->isCreated()->json();
    }

    public function update()
    {
        $validator = Validator::make(request()->all(),$this->rule());

        if($validator->fails()){
            return ApiResponse::make()->isNotValid($validator->errors())->json();
        }

        $category = Category::find(request('category'));

        if(!$category){
            return ApiResponse::make()->isNotFound()->json();
        }

        $category->update($validator->validated());

        return ApiResponse::make()->data($category)->isUpdated()->json();
    }

    public function destroy(){
        $category = Category::find(request('category'));

        //if category is not found, abort
        if(!$category){
            return ApiResponse::make()->isNotFound()->json();
        }

        //if category has one or more products, abort
        if($category->products->first()){
            $message = 'Kategori tidak bisa dihapus karena ada produk yg terhubung dengan kategori ini';
            return ApiResponse::make()->isNotAllowed($message)->json();
        }

        Category::destroy($category->id);

        return ApiResponse::make()->isDeleted()->json();
    }

    private function rule()
    {
        return [
            'name' => 'required',
            'type_id' => 'required|exists:types,id',
            'desc' => ''
        ];
    }
}
