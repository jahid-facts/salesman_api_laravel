<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\EmployeeTask;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class GroupAssignApiController extends Controller
{
    /**
     * Display a list of groups or a specific group by ID.
     *
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showUserGroup($id = null)
    {
        try {
            if ($id === null) {
                $groups = Group::all();
                if ($groups->isEmpty()) {
                    return response()->json(['message' => 'Groups not found'], 404);
                } else {
                    return response()->json(['groups' => $groups], 200);
                }
            } else {
                $group = Group::findOrFail($id);
                return response()->json(['group' => $group->groupByshops], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Assign a group to a user along with associated shops as tasks.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupAssignByUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'user_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->all(), 422);
            }

            $group = Group::findOrFail($request->group_id);
            $user = User::findOrFail($request->user_id);

            foreach ($group->groupByshops as $shop) {
                $task = new EmployeeTask($shop->toArray());
                $task->user_id = $user->id;
                $task->save();
            }

            $user->userBygroup()->attach($group->id);

            return response()->json(['success' => 'Group assigned'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a user from all groups.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function userGroupDelete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->userBygroup()->detach();

            return response()->json(['success' => 'User removed from all groups'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
