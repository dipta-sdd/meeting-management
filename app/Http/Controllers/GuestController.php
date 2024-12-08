<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}