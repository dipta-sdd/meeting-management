<?php

namespace App\Http\Controllers;

use App\Models\slot;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'start' => 'required',
            'end' => 'required',
            'date' => 'required'
        ]);

        $slot = new slot([
            'date' => $request->input('date'),
            'start' => $request->input('start'),
            'end' => $request->input('end'),
            'host' => auth()->user()->id
        ]);
        $slot->save();

        return response()->json($slot);
    }

    public function read($id = null)
    {
        $query = slot::selectRaw('slots.*, users.name as host_name')
            ->join('users', 'slots.host', '=', 'users.id')
            ->leftJoin('bookings', 'slots.booking_id', '=', 'bookings.id');

        if ($id) {
            $query->where('slots.id', $id);
            $slot = $query->first();
        } else {
            $query->where('slots.host', auth()->user()->id);
            $slot = $query->get();
        }

        return response()->json($slot);
    }

    public function update(Request $request, $id)
    {

        $slot = slot::findOrFail($id);
        $slot->update($request->all());

        return response()->json($slot);
    }

    public function delete($id)
    {
        $slot = slot::findOrFail($id);
        $slot->delete();

        return response()->json(null, 204);
    }
}
