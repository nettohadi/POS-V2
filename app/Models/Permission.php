<?php

namespace App\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    use HasFactory;
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
