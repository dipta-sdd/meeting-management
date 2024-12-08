<?php

namespace App\Http\Controllers;

use App\Models\recurrent;
use Illuminate\Http\Request;

class RecurrentSlotController extends Controller
{

    public function create(Request $request)
    {
        $data = $request->validate([
            'start' => 'required',
            'end' => 'required',
            'days' => 'required|array',
            'days.*' => 'string',
        ]);
        $data['host'] = auth()->user()->id;
        $slots = [];
        foreach ($data['days'] as $day) {
            $slots[] = recurrent::create([
                'day' => $day,
                'start' => $data['start'],
                'end' => $data['end'],
                'host' => $data['host'],
            ]);
            // dd(json_encode($slots));

        }
        return response()->json($slots);
    }

    public function read($id = null)
    {
        $query = recurrent::selectRaw('recurrents.day ,TIME(recurrents.start) as start, TIME(recurrents.end) as end, users.name as host_name')
            ->join('users', 'recurrents.host', '=', 'users.id');

        if ($id) {
            $query->where('recurrents.id', $id);
            $slot = $query->first();
        } else {
            $query->where('recurrents.host', auth()->user()->id);
            $slot = $query->get();
        }

        return response()->json($slot);
    }

    public function update(Request $request, $id)
    {
        $slot = recurrent::findOrFail($id);
        $slot->fill($request->all());
        $slot->save();
        return response()->json($slot);
    }

    public function delete($id)
    {
        recurrent::destroy($id);
        return response()->json(['message' => 'Slot deleted successfully']);
    }
}
