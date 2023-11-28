<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\User;


class EmployeeTask extends Model
{
    use HasFactory;

    public function assignByShop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }  
      
    public function assignByUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }  
}


