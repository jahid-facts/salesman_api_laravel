<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        $admin = User::where('role','admin')->get()->count();
        $disabledSalesmans = User::where('status','disabled')->where('role','salesman')->get()->count();
        $activeSalesmans = User::where('status','active')->where('role','salesman')->get()->count();

        $disabledShops = Shop::where('status','disabled')->whereNot('status','on_delete')->get()->count();
        $activeShops = Shop::where('status','active')->whereNot('status','on_delete')->get()->count();
        $shops = Shop::whereNot('status','on_delete')->get()->count();

        return response()->json(['admin'=>$admin, 'activeSalesmans' => $activeSalesmans,'disabledSalesmans'=>$disabledSalesmans, 'shops' => $shops, 'disabledShops'=>$disabledShops, 'activeShops'=>$activeShops], 200);
    }
}

