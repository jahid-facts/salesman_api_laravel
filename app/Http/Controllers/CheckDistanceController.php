<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckDistanceController extends Controller
{
    
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

        $dLat = $this->toRadians(this.absoluteValue($endLatitude - $startLatitude));
        $dLon = $this->toRadians(this.absoluteValue($endLongitude - $startLongitude));

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
                $tasks = EmployeeTask::where('status', 'in-completed')->with('assignByShop')->get();
    
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
                            $user->update([
                                'distance' => $distance,
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
    
    
}
