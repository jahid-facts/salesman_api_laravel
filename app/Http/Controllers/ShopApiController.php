<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Shop;
use App\Imports\ShopImport;


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
                $shops = Shop::get();
                return response()->json(['shop' => $shops], 200);
            } else {
                $shop = Shop::findOrFail($id);
                return response()->json(['shop' => $shop], 200);
            }
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
            Shop::findOrFail($id)->delete();
            return response()->json([
                'message' => 'Shop has been deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
