<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Group;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'shop_code',
        'number',
        'address',
        'latitude',
        'longitude',
        'longitude',
        'rc',
        'rc1',
        'status',
    ];

    public function shopBygroups()
    {
        return $this->belongsToMany(Group::class);
    }
}
