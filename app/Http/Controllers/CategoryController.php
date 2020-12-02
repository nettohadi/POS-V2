<?php

namespace App\Http\Controllers;

use App\Libs\MyResponse;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        return MyResponse::make()->data($category)->json();
    }

    public function show()
    {
        $category = Category::find(request('category'));
        return $category ? MyResponse::make()->data($category)->json() : MyResponse::make()->isNotFound()->json();
    }
}
