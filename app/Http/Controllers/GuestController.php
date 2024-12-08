<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function search_name(Request $request, $name = null)
    {
        $hosts = User::selectRaw('users.name as host_name, users.id')
            ->where('name', 'like', '%' . ($name ?? '') . '%')
            ->orderBy($request->sort ?? 'name', $request->order ?? 'asc')
            ->get();
        return response()->json($hosts);
    }
    public function search_slot(Request $request, $id)
    {
        $slots = DB::table(DB::raw('(select slots.* , DATE(start) as startDate, DATE(end) as endDate ,  TIME(start) as startTime, TIME(end) as endTime FROM slots where host = ' . $id . ') as s'))
            ->selectRaw('s.*')
            ->where('s.startDate', '>=', $request->input('start'))
            ->where('s.endDate', '<=', $request->input('end'))
            // ->whereRaw('TIME(s.startTime) >= TIME(' . $request->input('start') . ')')
            // ->whereRaw('TIME(s.endTime) <= TIME(' . $request->input('end') . ')')
            ->get();
        return response()->json($slots);
    }
}
