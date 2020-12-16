<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function index(){
        $name    = request()->get('name') ?? null;
        $perPage = request()->get('perPage') ?? 10;

        $units = Unit::filterByName($name)->paginate($perPage);
        return ApiResponse::make()->paginator($units)->json();
    }

    public function show()
    {
        $unit = Unit::find(request('unit'));
        return $unit ? ApiResponse::make()->data($unit)->json()
                     : ApiResponse::make()->isNotFound()->json();
    }

    public function store(){
        $validator = Validator::make(request()->all(), $this->rule());

        if($validator->fails()){
            return ApiResponse::make()
                   ->data(request()->all())
                   ->isNotValid($validator->errors())
                   ->json();

        }

        $unit = Unit::create($validator->validated());

        return ApiResponse::make()->data($unit)->isCreated()->json();

    }

    public function update(){

        $unit = Unit::find(request()->unit);

        //if unit is not found, return early
        if(!$unit){return ApiResponse::make()->isNotFound()->json();}

        $validator = Validator::make(request()->all(), $this->rule());
        if($validator->fails()){
            return ApiResponse::make()->data(request()->all())
                ->isNotValid($validator->errors())
                ->json();

        }

        $unit->update($validator->validated());

        return ApiResponse::make()->data($unit)->isUpdated()->json();

    }

    public function destroy(){
        $unit = Unit::find(request('unit'));

        //if unit is not found, return early
        if(!$unit){
            return ApiResponse::make()->isNotFound()->json();
        }

        //if unit has one or more products, return early
        if($unit->products->first()){
            $message = 'Satuan tidak bisa dihapus karena ada produk yg terhubung dengan satuan ini';
            return ApiResponse::make()->isNotAllowed($message)->json();
        }

        Unit::destroy($unit->id);

        return ApiResponse::make()->isDeleted()->json();
    }

    private function rule(){
        return [
            'name' => 'required',
            'desc' => ''
        ];
    }
}
