<?php

namespace App\Http\Controllers;

use App\Models\EmployeeTask;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TaskAssignApiController extends Controller
{
    public function showTask($id = null)
    {
        try {
            $tasks = ($id === null)
                ? EmployeeTask::all()
                : EmployeeTask::where('user_id', $id)->with('assignByShop')->get();

            return response()->json(['task' => $tasks], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function findTask($id, $date)
    {
        try {
            $tasks = EmployeeTask::where('user_id', $id)
                ->whereDate('assign_date', '=', $date)
                ->orderBy('rc', 'asc')
                ->with('assignByShop')
                ->get();

            if ($tasks->isEmpty()) {
                return response()->json(['message' => 'No tasks found.', 'task'=>[]], 200);
            }

            return response()->json(['task' => $tasks], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function taskAssign(Request $request)
    {
        try {
            if ($request->isMethod('POST')) {
                $validator = Validator::make($request->all(), [
                    'shop_id' => 'required|array',
                    'user_id' => 'required',
                    'assign_date' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                $shop_ids = $request->shop_id;
                $user_id = $request->user_id;
                $assign_date = $request->assign_date;

                foreach ($shop_ids as $shop_id) {
                    $shop_details = Shop::find($shop_id);
                    $task = new EmployeeTask();
                    $task->user_id = $user_id;
                    $task->shop_id = $shop_details->id;
                    $task->assign_date = $assign_date;
                    $task->rc = $shop_details->rc;
                    $task->save();
                }

                return response()->json([
                    'message' => 'Task has been assigned'
                ], 200);
            }

            return response()->json([
                'message' => 'Invalid request method'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
        public function updateTaskStatus(Request $request, $id)
        {
            try {
                // Validate the request
                $validator = Validator::make($request->all(), [
                    'status' => 'required',
                ]);
        
                // If validation fails, return error response
                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }
        
                // Update user information if userId is provided
                if ($request->has('userId')) {
                    $user = User::findOrFail($request->userId);
                    if ($user) {
                        $user->update([
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                        ]);
                    }
                }
        
                // Update EmployeeTask status
                EmployeeTask::where('id', $id)->update([
                    'status' => $request->status,
                    'distance' => $request->distance,
                ]);
        
                // Return success response
                return response()->json([
                    'message' => 'EmployeeTask status has been updated'
                ], 202);
            } catch (\Exception $e) {
                // Return error response in case of exception
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }



    public function taskDelete($id)
    {
        try {
            EmployeeTask::find($id)
                // ->whereDate('created_at', '=', date("Y-m-d"))
                ->delete();

            return response()->json([
                'message' => 'Tasks for the day have been deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
