<?php

namespace App\Http\Controllers;

use App\Libs\MyResponse;
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
        return MyResponse::make()->paginator($category)->json();
    }

    public function show()
    {
        $category = Category::with('type')->find(request('category'));
        return $category
            ? MyResponse::make()->data($category)->json()
            : MyResponse::make()->isNotFound()->json();
    }

    public function store()
    {
        $validator = Validator::make(request()->all(),$this->rule());

        if($validator->fails()){
            return MyResponse::make()->isNotValid($validator->errors())->json();
        }

        $category = Category::create($validator->validated())->load('type');

        return MyResponse::make()->data($category)->isCreated()->json();
    }

    public function update()
    {
        $validator = Validator::make(request()->all(),$this->rule());

        if($validator->fails()){
            return MyResponse::make()->isNotValid($validator->errors())->json();
        }

        $category = Category::find(request('category'));

        if(!$category){
            return MyResponse::make()->isNotFound()->json();
        }

        $category->update($validator->validated());

        return MyResponse::make()->data($category)->isUpdated()->json();
    }

    public function destroy(){
        $category = Category::find(request('category'));

        //if category is not found, return early
        if(!$category){
            return MyResponse::make()->isNotFound()->json();
        }

        Category::destroy($category->id);

        return MyResponse::make()->isDeleted()->json();
    }

    private function rule()
    {
        return [
            'name' => 'required',
            'type_id' => 'required|exists:types,id',
            'desc' => 'nullable'
        ];
    }
}
