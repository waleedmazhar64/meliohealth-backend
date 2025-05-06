<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function getUsers()
    {
        $users = User::select('id','name', 'email', 'role', 'status', 'amount')->get();
        return response()->json(['users' => $users]);
    }

    public function getPlans()
    {
        $free = \DB::table('settings')->where('key', 'free_plan')->value('value');
        $premium = \DB::table('settings')->where('key', 'premium_plan')->value('value');

        return response()->json([
            'free_plan' => $free ?? '0.00',
            'premium_plan' => $premium ?? '0.00'
        ]);
    }

    public function updatePlans(Request $request)
    {
        $request->validate([
            'free_plan' => 'required|numeric',
            'premium_plan' => 'required|numeric'
        ]);

        // Save to DB or config
        \DB::table('settings')->updateOrInsert(['key' => 'free_plan'], ['value' => $request->free_plan]);
        \DB::table('settings')->updateOrInsert(['key' => 'premium_plan'], ['value' => $request->premium_plan]);

        return response()->json(['message' => 'Plans updated']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json(['message' => 'User status updated']);
    }

}
