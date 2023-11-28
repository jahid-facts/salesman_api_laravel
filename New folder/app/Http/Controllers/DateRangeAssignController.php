<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DateRangeAssign;

class DateRangeAssignController extends Controller
{
    public function dateRangeUser($id)
    {
        try {
            $user_date = DateRangeAssign::where('user_id', $id)->first();
            return response()->json(['user' => $user_date], 200);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function dateRangeAssign(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), 422);
            }
                $user_date = DateRangeAssign::where('user_id', $request->user_id)->first();
                if ($user_date->isEmpty()) {
                    DateRangeAssign::create([
                        'user_id'=> $request->user_id,
                        'day_1'=>$request->day_1,
                        'day_2'=>$request->day_2,
                        'day_3'=>$request->day_3,
                        'day_4'=>$request->day_4,
                        'day_5'=>$request->day_5,
                        'day_6'=>$request->day_6,
                        'day_7'=>$request->day_7,
                        'day_8'=>$request->day_8,
                        'day_9'=>$request->day_9,
                    ]);
                    return response()->json(['message' => 'Date has been assigned'], 200);
                } else {
                    DateRangeAssign::where('user_id', $request->user_id)->update([
                        'day_1'=>$request->day_1,
                        'day_2'=>$request->day_2,
                        'day_3'=>$request->day_3,
                        'day_4'=>$request->day_4,
                        'day_5'=>$request->day_5,
                        'day_6'=>$request->day_6,
                        'day_7'=>$request->day_7,
                        'day_8'=>$request->day_8,
                        'day_9'=>$request->day_9,
                    ]);
                    return response()->json(['message' => 'Date has been assigned'], 200);
                }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
