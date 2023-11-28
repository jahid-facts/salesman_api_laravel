<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\User;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function groupByshops()
    {
        return $this->belongsToMany(Shop::class)->withTimestamps();
    }   
    public function groupByuser()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }   
}
