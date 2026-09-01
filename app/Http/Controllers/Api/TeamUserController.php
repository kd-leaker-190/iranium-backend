<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamUserResource;
use App\Models\TeamUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Support\Responses\ApiResponse;

class TeamUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamUsers = TeamUser::with(['team'])
            ->where('team_id', Auth::guard('team')->id())
            ->latest()
            ->get();

        return ApiResponse::success(TeamUserResource::collection($teamUsers));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'grade_level' => ['required', 'string', 'min:3', 'max:20'],
            'birth_date' => ['required', 'date'],
            'role' => [
                'nullable',
                'in:successor,human_resource,academic,constructiveness_and_efficiency,cultural,education_and_training_of_famous_assistants,journalism,support,inspection,physical_education'
            ],
        ]);

        if ($validator->fails()) {
            return ApiResponse::fail('خطا در ایجاد عضو تیم دوباره تلاش کنید.', null, $validator->errors());
        }

        $teamUser = TeamUser::create([
            'team_id' => Auth::guard('team')->id(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'grade_level' => $request->grade_level,
            'birth_date' => $request->birth_date,
            'role' => $request->role,
        ]);

        return ApiResponse::success(new TeamUserResource($teamUser), '', 'عضو جدید ساخته شد.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'last_name' => ['required', 'string', 'min:3', 'max:255'],
            'grade_level' => ['required', 'string', 'min:3', 'max:20'],
            'birth_date' => ['required', 'date'],
            'role' => [
                'nullable',
                'in:successor,human_resource,academic,constructiveness_and_efficiency,cultural,education_and_training_of_famous_assistants,journalism,support,inspection,physical_education'
            ],
        ]);

        if ($validator->fails()) {
            return ApiResponse::fail('خطا در ویرایش عضو تیم دوباره تلاش کنید.', null, $validator->errors());
        }

        try {
            $teamId = Auth::guard('team')->id();

            $teamUser = TeamUser::where('id', $id)
                ->where('team_id', $teamId)
                ->firstOrFail();

            $teamUser->update($validator->validated());

            return ApiResponse::success(new TeamUserResource($teamUser), '', 'اطلاعات عضو تیم ویرایش شد.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::fail('خطا', null, 'عضو مورد نظر یافت نشد.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $teamId = Auth::guard('team')->id();

            $teamUser = TeamUser::where('id', $id)
                ->where('team_id', $teamId)
                ->firstOrFail();

            $teamUser->delete();
            return ApiResponse::success(new TeamUserResource($teamUser), '', 'عضو تیم حذف شد.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::fail('خطا', null, 'عضو مورد نظر یافت نشد.');
        }
    }
}
