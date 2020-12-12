<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UCode extends BaseModel
{
    use HasFactory;
    protected $table = 'ucodes';
    protected $guarded = [];

    public static function generate(string $str)
    {
        $uCode = self::updateOrCreate(['str' => $str], ['str' => $str]);
        $uCode->increment('last_order');

        return "{$uCode->str}{$uCode->last_order}";
    }
}
