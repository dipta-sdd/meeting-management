<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\Meeting; 
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function bookMeeting(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'guest_id' => 'nullable|exists:guests,id', // Assuming you have a guests table
            'meeting_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the meeting
        $meeting = Meeting::create([
            'guest_id' => $request->guest_id,
            'meeting_date' => $request->meeting_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return response()->json($meeting, 201);
    }

    public function getMeetings()
    {
        // Get the authenticated user's ID
        $userId = auth()->user()->id; // Assuming you have authentication set up

        // Retrieve meetings for the authenticated user
        $meetings = Meeting::where('guest_id', $userId)->get();

        return response()->json($meetings);
    }
}
