<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmployeeTask;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserApiController extends Controller
{
    public function showUser($id = null)
    {
        try {
            if ($id === null) {
                $users = User::get();
                if ($users->isEmpty()) {
                    return response()->json([
                        'Users not found'
                    ], 404);
                } else {
                    return response()->json(['users' => $users], 200);
                }
            } else {
                $user = User::find($id);
                return response()->json(['user' => $user], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addUser(Request $request)
    {
        try {
            // if ($request->isMethod('POST')) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required',
                    'number' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                // $user_code = IdGenerator::generate(['table' => 'users', 'field' => 'user_code', 'length' => 6, 'prefix' => date('y')]);

                $user = User::create([
                    'name' => $request->name,
                    'number' => $request->number,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                return response()->json([
                    'user' => $user,
                    'message' => 'User has been added'
                ], 200);
            // }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            if ($request->isMethod('PUT')) {
                // Uncomment the validator if needed
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'number' => 'required',
                    'status' => 'required',
                    'deviceId' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'status' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }

                $user = User::findOrFail($id);
                $user->update([
                    'name' => $request->name,
                    'number' => $request->number,
                    'email' => $request->email,
                    'status' => $request->status,
                    'password' => bcrypt($request->password),
                ]);

                return response()->json([
                    'message' => 'User has been updated'
                ], 200);
            }

            return response()->json([
                'message' => 'Invalid request method'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    
    private function toRadians($degree)
    {
        return $degree * M_PI / 180;
    }

    private function calculateDistance(
        $startLatitude,
        $startLongitude,
        $endLatitude,
        $endLongitude
    ) {
        $earthRadius = 6371000.0;

        $dLat = $this->toRadians($this->absoluteValue($endLatitude - $startLatitude));  // Fix the use of $this->absoluteValue
        $dLon = $this->toRadians($this->absoluteValue($endLongitude - $startLongitude));  // Fix the use of $this->absoluteValue

        $a = sin($dLat / 2) * sin($dLat / 2) +
            sin($dLon / 2) * sin($dLon / 2) * cos($this->toRadians($startLatitude)) * cos($this->toRadians($endLatitude));
        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }

    private function absoluteValue ($value) {
        if($value < 0){
            return $value * -1;
        }
        return $value;
    }


    public function userLocation(Request $request, $id)
    {
        try {
            if ($request->isMethod('PUT')) {
    
                // Uncomment the validator if needed
                $validator = Validator::make($request->all(), [
                    'latitude' => 'required',
                    'longitude' => 'required',
                ]);
    
                if ($validator->fails()) {
                    return response()->json($validator->errors()->all(), 422);
                }
    
                // distance calculate
                $tasks = EmployeeTask::where('assign_date', date("Y-m-d"))->where('user_id', $id)->where('status', 'in-completed')->with('assignByShop')->get();
    
                $user = User::findOrFail($id);
    
                foreach ($tasks as $task) {
                    $assignByShop = $task->assignByShop;
    
                    if ($assignByShop) {
                        $endLatitude = $assignByShop->latitude;
                        $endLongitude = $assignByShop->longitude;
    
                        $startLatitude = $request->latitude;
                        $startLongitude = $request->longitude;
    
                        $distance = $this->calculateDistance(
                            $startLatitude,
                            $startLongitude,
                            $endLatitude,
                            $endLongitude
                        );

                        if ($distance < 20) {
                            EmployeeTask::where('id', $task->id)->update([
                                'distance' => $distance,
                                'status' => "completed",
                            ]);
                        }
                    }
                }
    
                // Update user location only if needed
                $user->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
    
                return response()->json([
                    'message' => 'User has been location updated'
                ], 200);
            }
    
            return response()->json([
                'message' => 'Invalid request method'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function statusUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'User has been updated status'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
           User::findOrFail($id)->delete();
            return response()->json([
                'message' => 'User has been deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function userLogin(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                return response()->json(['message' => 'Method not allowed'], 404);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), 422);
            }

            $userInfo = User::where('email', $request->email)->first();

            if ($userInfo) {
                if ((empty($userInfo->deviceId) || $userInfo->deviceId === null) && (Hash::check($request->password, $userInfo->password))) {
                    $userInfo->update(['deviceId' => $request->deviceId]);

                    return response()->json([
                        'user' => $userInfo,
                        'validation' => 'Device ID is registered now'
                    ], 200);
                } elseif ($userInfo->role == "salesman" && $userInfo->deviceId == $request->deviceId && Hash::check($request->password, $userInfo->password)) {
                    return response()->json([
                        'user' => $userInfo,
                        'validation' => 'Passed'
                    ], 200);
                } else {
                    return response()->json([
                        'user' => $userInfo,
                        'validation' => 'Failed'
                    ], 401);
                }
            }

            return response()->json(['message' => 'Wrong information'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function adminLogin(Request $request)
    {
        try {
            if (!$request->isMethod('post')) {
                return response()->json(['message' => 'Method not allowed'], 401);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), 422);
            }

            $userInfo = User::where('email', $request->email)->first();
            if ($userInfo) {
                if ($userInfo->role == "admin" && Hash::check($request->password, $userInfo->password)) {
                    return response()->json([
                        'user' => $userInfo,
                        'message' => 'login successfully'
                    ], 200);
                }
            }

            return response()->json(['message' => 'Wrong information'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
