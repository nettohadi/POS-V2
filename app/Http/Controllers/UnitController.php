<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Libs\ApiResponse;
use App\Models\Unit;
use App\Http\Controllers\Base\BaseController;

class UnitController extends BaseController
{
    public function index(){
        $name    = request()->get('name') ?? null;
        $perPage = request()->get('perPage') ?? 10;

        $units = Unit::filterByName($name)->paginate($perPage);
        return ApiResponse::make()->paginator($units)->json();
    }

    public function show()
    {
        $unit = Unit::findOrThrow(request('unit'));
        return ApiResponse::make()->data($unit)->json();
    }

    public function store(UnitRequest $request){

        $unit = Unit::create($request->validated());

        return ApiResponse::make()->data($unit)->isCreated()->json();

    }

    public function update(UnitRequest $request){

        $unit = Unit::findOrThrow($request->unit);

        $unit->update($request->validated());

        return ApiResponse::make()->data($unit)->isUpdated()->json();

    }

    public function destroy(){
        $unit = Unit::findOrThrow(request('unit'));

        Unit::destroy($unit->id);

        return ApiResponse::make()->isDeleted()->json();
    }
}
