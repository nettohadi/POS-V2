<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Return filterable paginated list of product.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $perPage = request()->query('perPage');
        $productName = request()->query('product');
        $outlet = request()->query('outlet');

        $recipes = Recipe::filterByProductName($productName)
                        ->filterByOutlet($outlet)
                        ->with('product','outlet')
                        ->paginate($perPage);

        return ApiResponse::make()->paginator($recipes)->json();
    }
}
