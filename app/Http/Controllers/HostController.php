<?php

namespace App\Http\Controllers;

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
        $slots = slot::selectRaw(' bookings.* , users.name as guest_name')
            ->where('host', auth()->user()->id)
            ->where('host_seen', null)
            ->join('users', 'bookings.guest', '=', 'users.id')
            ->orderBy('updated_at', 'asc');
        if ($req->has('updated_at')) {
            $slots->where('updated_at', '>', $req->input('updated_at')); // Replace 'updated_at', 'value...')
        }
        $slots = $slots->get();


        return response()->json($slots);
    }
    // public function one_time_slots(Request $request){
    //     $slots = slot::selectRaw('TIME(start) as start, TIME(end) as end')
    //         ->where('host', auth()->user()->id)
    //         ->where()
    //         ->get();
    //     return response()->json($slots);
    // }
}
