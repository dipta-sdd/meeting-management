<?php

namespace App\Http\Controllers;

use App\Models\booking;
use App\Models\recurrent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function search_name(Request $request, $name = null)
    {
        $hosts = User::selectRaw('users.name as label,users.name as value, users.id')
            ->where('name', 'like', '%' . ($request->term ?? '') . '%')
            ->where('role', 'host')
            ->orderBy($request->sort ?? 'name', $request->order ?? 'asc')
            ->get();
        return response()->json($hosts);
    }
    public function search_slot(Request $request, $id)
    {
        // this sql query is used to get the slots of a host and apply the recurrent rules to it
        // the query is divided into two parts, the first one is the inner select that gets the slots
        // and the recurrents rules for the host, the second part is the outer select that applies the recurrent rules to the slots
        // the recurrent rules are applied by using the IF function to check if the start time of the slot is before the start time of the recurrent rule
        // if it is, then the start time of the slot is used, otherwise the start time of the recurrent rule is used
        // the same logic is applied to the end time of the slot
        // the resulting query is a list of slots with their start and end times adjusted according to the recurrent rules
        $slots = DB::table(DB::raw('(select slots.id , slots.start, slots.end, slots.host, 
IF(TIME(slots.start) < recurrents.start ,
TIME(slots.start) , IF(TIME(slots.start) < recurrents.end , 
recurrents.start, TIME(slots.start))) as startTime,
IF(TIME(slots.end) > recurrents.end ,TIME(slots.end) , IF(TIME(slots.end) > recurrents.start , recurrents.end, TIME(slots.end))) as endTime,
DATE(slots.start) as startDate,DATE(slots.end) as endDate
FROM slots LEFT JOIN recurrents on recurrents.day = DAYNAME(slots.start) and slots.host = recurrents.host where slots.host  = ' . $id . ') as s'))
            ->selectRaw(' s.id,s.host, s.startTime, s.endTime, s.startDate,
      TIMEDIFF(IF(s.endTime <= "' . $request->input('endTime') . '" , s.endTime, "' . $request->input('endTime') . '"),
      IF(s.startTime >= "' . $request->input('startTime') . '" , s.startTime, "' . $request->input('startTime') . '")
      )
   as v,

   IF(s.startTime >= "' . $request->input('startTime') . '" , s.startTime, "' . $request->input('startTime') . '") as bestStart,
   IF(s.endTime <= "' . $request->input('endTime') . '" , s.endTime, "' . $request->input('endTime') . '") as bestEnd,
   DAYNAME(s.startDate) as day_name')
            ->where('s.start', '>=', $request->input('startDate'))
            ->where('s.end', '<=', $request->input('endDate'))
            ->whereRaw(' ( (s.startTime >= "' . $request->input('startTime') . '" and s.endTime <= "' . $request->input('endTime') . '") or ( s.startTime <= "' . $request->input('endTime') . '" and s.endTime >= "' . $request->input('endTime') . '") or ( s.startTime <= "' . $request->input('startTime') . '" and s.endTime >= "' . $request->input('startTime') . '") )')
            ->orderBy('s.startDate', 'asc')
            ->get();

        $final_slots = [];
        $i = 0;

        for ($date = $request->input('startDate'); $date <= $request->input('endDate'); $date = date('Y-m-d', strtotime($date . ' +1 days'))) {
            if (isset($slots[$i]) && $date == $slots[$i]->startDate) {
                while (isset($slots[$i]) && $date == $slots[$i]->startDate) {
                    $slots[$i]->v = (string) substr($slots[$i]->v, 0, 8);
                    $final_slots[] = $slots[$i];
                    $i++;
                }
                continue;
            }

            $slot = DB::table(DB::raw('(SELECT * FROM (select recurrents.* ,DAYNAME("' . $date . '") AS day_name  from `recurrents` where  host = ' . $id . ') as x WHERE day = day_name) as s'))
                ->selectRaw('s.id , s.host,DATE("' . $date . '") as startDate, s.start as startTime, s.end as endTime,
                TIMEDIFF(IF(s.end <= "' . $request->input('endTime') . '" , s.end, "' . $request->input('endTime') . '"),
                IF(s.start >= "' . $request->input('startTime') . '" , s.start, "' . $request->input('startTime') . '")
                )
             as v,
                IF(s.start >= "' . $request->input('startTime') . '" , s.start, "' . $request->input('startTime') . '") as bestStart,
                IF(s.end <= "' . $request->input('endTime') . '" , s.end, "' . $request->input('endTime') . '") as bestEnd,
                s.day as day_name')
                // ->whereRaw('s.day', 's.days')
                ->first();
            if (!$slot) {
                continue;
            }
            $slot->v = (string) substr($slot->v, 0, 8);
            $final_slots[] = $slot;
        }
        $slots = $final_slots;
        $final_slots = [];
        $maxVSlot = null;
        foreach ($slots as $slot) {
            if ($maxVSlot === null || $slot->v > $maxVSlot->v) {
                $maxVSlot = $slot;
            }
            $v = $slot->v;
            $v = (string) $slot->v;
            if (!$v[0] == '-') {
                $final_slots[] = $slot;
            }
        }




        return response()->json(['slots' => $final_slots, 'maxVSlot' => $maxVSlot]);
    }

    public function book_slot(Request $request)
    {
        $slot = booking::create([
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'host' => $request->input('host'),
            'guest' => auth()->user()->id,
        ]);
        return response()->json(['message' => 'Slot booked successfully', 'slot' => $slot]);
        return response()->json($slot);
    }
}
