<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShopGroupApiController extends Controller
{
    public function showShopGroup($id = null)
    {
        try {
            if ($id === null) {
                $groups = Group::with('groupByshops')->get();
                if ($groups->isEmpty()) {
                    return response()->json(['message' => 'Groups not found'], 404);
                } else {
                    return response()->json(['groups' => $groups], 200);
                }
            } else {
                $group = Group::findOrFail($id)->groupByshops;
                return response()->json(['group' => $group], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function shopAssignByGroup(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $validator = Validator::make($request->all(), [
                    'group_name' => 'required|unique:groups,name',
                    'shop_id' => 'required'
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                $group = Group::create([
                    'name' => $request->group_name,
                ]);

                $group->groupByshops()->attach($request->shop_id);

                return response()->json([
                    'message' => 'Group has been created'
                ], 200);
            }

            return response()->json([
                'message' => 'Invalid request'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function shopGroupDelete($id)
    {
        try {
            $group = Group::findOrFail($id);
            $group->groupByshops()->detach();
            $group->delete();

            return response()->json([
                'message' => 'Group has been deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
