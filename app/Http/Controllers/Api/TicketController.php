<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCategoryResource;
use App\Http\Resources\TicketMessageResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\Support\Responses\ApiResponse;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::latest()
            ->where('team_id', Auth::guard('team')->id())
            ->get();

        return ApiResponse::success(TicketResource::collection($tickets));
    }

    public function ticketCategories()
    {
        $ticketCategories = TicketCategory::all();
        return ApiResponse::success(TicketCategoryResource::collection($ticketCategories));
    }

    public function show(Ticket $ticket)
    {
        $teamId = Auth::guard('team')->id();

        if ((int) $ticket->team_id !== (int) $teamId) {
            logger("Expected Team ID: {$ticket->team_id}, Actual: {$teamId}");
            return ApiResponse::fail('خطا', null, 'تیکت مورد نظر یافت نشد.', 404);
        }

        $ticket->load(['messages', 'ticketCategory']);
        return ApiResponse::success(new TicketResource($ticket));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string', 'max:255'],
            'ticket_category_id' => ['required', 'integer'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'body' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::fail('خطا در ثبت تیکت', null, $validator->errors(), 422);
        }

        $team = Auth::guard('team')->user();

        DB::beginTransaction();

        try {
            $ticket = Ticket::create([
                'team_id' => Auth::guard('team')->id(),
                'subject' => $request->subject,
                'ticket_category_id' => $request->ticket_category_id,
                'priority' => $request->priority,
                'status' => TicketStatus::WAITING_FOR_ADMIN
            ]);

            $ticket->messages()->create([
                'ticket_id' => $ticket->id,
                'sender_id' => $team->id,
                'sender_type' => \get_class($team),
                'body' => $request->body,
            ]);

            DB::commit();

            $ticket->load('messages');

            return ApiResponse::success(new TicketResource($ticket), 201, 'تیکت با موفقیت ثبت شد.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return ApiResponse::fail('خطا در ثبت تیکت', null, $exception->getMessage(), 422);
        }
    }

    public function sendMessage(Request $request, Ticket $ticket)
    {
        $validator = Validator::make($request->all(), [
            'body' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::fail('خطا در ثبت پیام', null, $validator->errors(), 422);
        }

        try {
            $teamId = auth('team')->id();

            if ((int) $ticket->team_id !== (int) $teamId) {
                return ApiResponse::fail('خطا', null, 'تیکت مورد نظر یافت نشد.', 404);
            }

            $team = Auth::guard('team')->user();

            $newMessage = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_id' => $team->id,
                'sender_type' => \get_class($team),
                'body' => $request->body
            ]);

            $newMessage->ticket()->update([
                'status' => TicketStatus::WAITING_FOR_ADMIN
            ]);

            return ApiResponse::success(new TicketMessageResource($newMessage), 201, 'پیام شما با موفقیت ثبت شد.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::fail('خطا', null, 'تیکت مورد نظر یافت نشد.');
        }
    }
}
