<?php

namespace App\Http\Controllers;

use App\Models\booking;
use App\Models\recurrent;
use App\Models\slot;
use Illuminate\Http\Request;

class HostController extends Controller
{
    //
    public function slots()
    {
        $slots = slot::selectRaw('TIME(start) as start, TIME(end) as end')
            ->union(
                recurrent::selectRaw('TIME(start) as start, TIME(end) as end')
            )
            ->orderByPivot('start', 'asc')
            ->get();

        return response()->json($slots);
    }
    public function notifications(Request $req)
    {
        $slots = booking::selectRaw(' bookings.* , users.name as guest_name')
            ->where('host', auth()->user()->id)
            ->where('host_seen', null)
            ->join('users', 'bookings.guest', '=', 'users.id')
            ->orderBy('updated_at', 'asc');
        if ($req->has('updated_at')) {
            $slots->where('updated_at', '>', $req->input('updated_at')); // Replace 'updated_at', 'value...')
        }
        $slots = $slots->get();

        booking::where('host', auth()->user()->id)
            ->where('host_seen', null)
            ->update(['host_seen' => false]);
        return response()->json($slots);
    }
    // public function one_time_slots(Request $request){
    //     $slots = slot::selectRaw('TIME(start) as start, TIME(end) as end')
    //         ->where('host', auth()->user()->id)
    //         ->where()
    //         ->get();
    //     return response()->json($slots);
    // }
    public function dashboard()
    {
        $chart = booking::selectRaw(' DISTINCT( DATE(bookings.start) ) as  day, COUNT(bookings.id) as slots_count')->where('host', auth()->user()->id)->groupBy('day')->orderBy('day', 'desc')->limit(5)->get();
        $days = $chart->pluck('day')->toArray();
        $slots_count = $chart->pluck('slots_count')->toArray();

        $trend = booking::selectRaw(' DISTINCT( DAYNAME( bookings.start) ) as  day, COUNT(bookings.id) as slots_count')->where('host', auth()->user()->id)->groupBy('day')->get();
        // $trend_days = $trend->pluck('day')->toArray();
        $trend_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $trend_slots_count = array_fill(0, 7, 0);
        foreach ($trend as $item) {
            $key = array_search($item->day, $trend_days);
            $trend_slots_count[$key] = $item->slots_count;
        }


        return response()->json(['bar' => ['days' => $days, 'slots_count' => $slots_count], 'trend' => ['days' => $trend_days, 'slots_count' => $trend_slots_count]]);
    }
    public function confirmBooking($id)
    {
        $bookings = booking::where('status', '=', 'Pending')->where('id', $id)->first();
        $others = booking::whereRaw('status = "Confirmed" and host = ' . auth()->user()->id . ' and ((start< "' . $bookings->start . '" and end> "' . $bookings->start . '") or (start< "' . $bookings->end . '" and end> "' . $bookings->end . '") or (start> "' . $bookings->start . '" and end< "' . $bookings->end . '"))')
            ->where('id', '!=', $id)
            ->get();

        if (count($others) > 0) {
            return response()->json(['error' => 'There is another booking in this period', 'others' => $others], 409);
        }
        $bookings->status = 'Confirmed';
        $bookings->save();

        return response()->json($bookings);
    }
}
