<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotifyResource;
use Illuminate\Support\Facades\DB;
use Modules\Support\Responses\ApiResponse;

class NotifyController extends Controller
{
    public function teamNotifications()
    {
        $team = \Auth::guard('team')->user();

        $notifs = $team->notifies()
            ->orderByDesc('release')
            ->get();

        return ApiResponse::success(NotifyResource::collection($notifs));
    }

    public function unreadCount()
    {
        $team = \Auth::guard('team')->user();

        $count = DB::table('notify_teams')
            ->where('team_id', $team->id)
            ->where('is_read', false)
            ->count();

        return ApiResponse::success(['count' => $count]);
    }

    public function markAllRead()
    {
        $team = \Auth::guard('team')->user();

        DB::table('notify_teams')
            ->where('team_id', $team->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return ApiResponse::success(['message' => 'تمام پیام ها خوانده شد. ✅']);
    }

    public function markOneRead($notify)
    {
        $team = \Auth::guard('team')->user();

        DB::table('notify_teams')
            ->where('team_id', $team->id)
            ->where('notify_id', $notify)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return ApiResponse::success(['message' => 'پیام خوانده شد ✅']);
    }
}
