<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Libs\ApiResponse;
use App\Models\Category;
use App\Http\Controllers\Base\BaseController;

class CategoryController extends BaseController
{
    public function index()
    {
        $name    = request()->query('name');
        $perPage = request()->query('perPage',10);

        $category = Category::filterByName($name)->with('type')->paginate($perPage);
        return ApiResponse::make()->paginator($category)->json();
    }

    public function show()
    {
        $category = Category::with('type')->findOrThrow(request('category'));
        return ApiResponse::make()->data($category)->json();
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        $category = Category::findOrThrow($category->id);

        return ApiResponse::make()->data($category)->isCreated()->json();
    }

    public function update(CategoryRequest $request)
    {
        $category = Category::findOrThrow(request('category'));

        $category->update($request->validated());

        return ApiResponse::make()->data($category)->isUpdated()->json();
    }

    public function destroy(){
        $category = Category::findOrThrow(request('category'));

        Category::destroy($category->id);

        return ApiResponse::make()->isDeleted()->json();
    }
}
