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
            'day' => 'required',
        ]);
        $data['host'] = auth()->user()->id;
        $slot = recurrent::create($data);
        return response()->json($slot);
    }

    public function read($id = null)
    {
        $query = recurrent::selectRaw('recurrents.*, users.name as host_name')
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
