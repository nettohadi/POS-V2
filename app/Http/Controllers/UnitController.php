<?php

namespace App\Http\Controllers;

use App\Lib\MyResponse;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index(){
        $units = Unit::all();
        return MyResponse::make()->data($units)->send();
    }

    public function show()
    {
        $unit = Unit::find(request('unit'));
        return $unit ? MyResponse::make()->data($unit)->send() : MyResponse::make()->isNotFound()->send();
    }

    public function store(){
        $validator = Validator::make(request()->all(), $this->rule());

        if($validator->fails()){
            return MyResponse::make()->data(request()->all())
                   ->isNotValid($validator->errors())
                   ->send();

        }

        $unit = Unit::create($validator->validated());

        return MyResponse::make()->data($unit)->isCreated()->send();

    }

    public function update(){

        $unit = Unit::find(request()->unit);

        //if unit is not found, return early
        if(!$unit){return MyResponse::make()->isNotFound()->send();}

        $validator = Validator::make(request()->all(), $this->rule());
        if($validator->fails()){
            return MyResponse::make()->data(request()->all())
                ->isNotValid($validator->errors())
                ->send();

        }

        $unit->update($validator->validated());

        return MyResponse::make()->data($unit)->isUpdated()->send();

    }

    public function destroy(){
        $unit = Unit::find(request('unit'));

        //if unit is not found, return early
        if(!$unit){
            return MyResponse::make()->isNotFound()->send();
        }

        Unit::destroy($unit->id);

        return MyResponse::make()->isDeleted()->send();
    }

    private function rule(){
        return [
            'name' => 'required',
            'desc' => ''
        ];
    }
}
