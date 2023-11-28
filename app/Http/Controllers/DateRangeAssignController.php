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
            
            if($user_date){
                return response()->json(['user' => $user_date], 200);
            }else{
                return response()->json([
                    'user' => (object)[
                        'user_id' => $id,
                        'day_1' => null,
                        'day_2' => null,
                        'day_3' => null,
                        'day_4' => null,
                        'day_5' => null,
                        'day_6' => null,
                        'day_7' => null,
                        'day_8' => null,
                        'day_9' => null,
                    ]
                ], 200);
                
            }
            
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function dateRangeAssign(Request $request)
    {
        try {
            if ($request->user_id) {
               
            $user_date = DateRangeAssign::where('user_id', $request->user_id)->first();
            
                if ($user_date) {
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
                } else {
                    
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
                }
            }
            //     else{
            //     return response()->json(['error' => "error"], 500);
            // }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
