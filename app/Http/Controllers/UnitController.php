<?php

namespace App\Http\Controllers;

use App\Libs\MyResponse;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index(){
        $perPage = request()->input('perPage') ?? 10;
        $units = Unit::paginate($perPage);
        return MyResponse::make()->paginator($units)->json();
    }

    public function show()
    {
        $unit = Unit::find(request('unit'));
        return $unit ? MyResponse::make()->data($unit)->json()
                     : MyResponse::make()->isNotFound()->json();
    }

    public function store(){
        $validator = Validator::make(request()->all(), $this->rule());

        if($validator->fails()){
            return MyResponse::make()
                   ->data(request()->all())
                   ->isNotValid($validator->errors())
                   ->json();

        }

        $unit = Unit::create($validator->validated());

        return MyResponse::make()->data($unit)->isCreated()->json();

    }

    public function update(){

        $unit = Unit::find(request()->unit);

        //if unit is not found, return early
        if(!$unit){return MyResponse::make()->isNotFound()->json();}

        $validator = Validator::make(request()->all(), $this->rule());
        if($validator->fails()){
            return MyResponse::make()->data(request()->all())
                ->isNotValid($validator->errors())
                ->json();

        }

        $unit->update($validator->validated());

        return MyResponse::make()->data($unit)->isUpdated()->json();

    }

    public function destroy(){
        $unit = Unit::find(request('unit'));

        //if unit is not found, return early
        if(!$unit){
            return MyResponse::make()->isNotFound()->json();
        }

        Unit::destroy($unit->id);

        return MyResponse::make()->isDeleted()->json();
    }

    private function rule(){
        return [
            'name' => 'required',
            'desc' => ''
        ];
    }
}
