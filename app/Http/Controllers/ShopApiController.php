<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shop;
use App\Imports\ShopImport;
use Carbon\Carbon;


class ShopApiController extends Controller
{
    public function rcFilter($rc1)
    {
        try {
            $shops = Shop::where('rc1', '=', $rc1)
                ->where('status', '=', "active")
                ->orderBy('rc', 'asc')
                ->get();

            if ($shops->isEmpty()) {
                return response()->json(['message' => 'No shops found.', 'task'=> [] ], 200);
            }

            return response()->json(['task' => $shops], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addShop(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            $file = $request->file('file');
            Excel::import(new ShopImport, $file);

            return response()->json(['message' => 'File uploaded successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading file: ' . $e->getMessage()], 500);
        }

    }

    public function showShop($id = null)
    {
        try {
            if ($id === null) {
                $shops = Shop::whereNot('status','on_delete')->get();
                return response()->json(['shop' => $shops], 200);
            } else {
                $shop = Shop::findOrFail($id);
                return response()->json(['shop' => $shop], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // find all shop where status is active and update status all on_delete
    public function onDeleteShop(){
        try {
            // Use an associative array to specify the fields to update
            Shop::where("status", "active")->update([
                'status' => 'on_delete',
                'on_deleted_at' => Carbon::now()->toDateTimeString(),
            ]);
            return response()->json([
               'message' => 'Shop status has been updated'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function onDeleteShopByRc($rc){
        try {
            // Use an associative array to specify the fields to update
            $rc = str_replace(' ', '', $rc);
            $updated = Shop::where('status', 'active')
                ->where('rc1', $rc)
                ->update([
                    'status' => 'on_delete',
                    'on_deleted_at' => Carbon::now(),
                ]);

            if ($updated) {
                return response()->json([
                    'message' => 'Shop status has been updated',
                    'status' => 'success'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No shop found with the specified rc.',
                    'status' => 'error'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function singleShop($id)
    {
        try {
            $shops = Shop::where('id', $id)
            ->select('id','name', 'address', 'number')
            ->first();
          
            if (is_null($shops)) {
                return response()->json(['message' => 'No shops found.', 'shop'=> [] ], 200);
            }

            return response()->json(['shop' => $shops], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function updateShops(Request $request, $id)
    {
        try {
            if ($request->isMethod('PUT')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'address' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                Shop::where('id', $id)->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'number' => $request->number,
                ]);

                return response()->json([
                    'message' => 'Shop has been updated'
                ], 202);
            }

            return response()->json([
                'message' => 'Invalid request'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateShop(Request $request, $id)
    {
        try {
            if ($request->isMethod('PUT')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'address' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                Shop::where('id', $id)->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'number' => $request->number,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'status' => $request->status,
                ]);

                return response()->json([
                    'message' => 'Shop has been updated'
                ], 202);
            }

            return response()->json([
                'message' => 'Invalid request'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function statusShop(Request $request, $id)
    {
        try {
            $shop = Shop::findOrFail($id);
            $shop->update([
                'status' => $request->status,
            ]);
            return response()->json([
                'message' => 'Shop has been updated status'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function deleteShop($id)
    {
        try {
            Shop::whereId($id)->update([
                'status' => 'on_delete',
                'on_deleted_at' => Carbon::now()->toDateTimeString(),
            ]);
            return response()->json([
                'message' => 'Shop has been deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
