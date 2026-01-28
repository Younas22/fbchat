<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    // Display all users
    public function index()
    {
        $users = User::with(['facebookPages', 'conversations', 'savedChats'])->get();
        return view('admin.user-management', compact('users'));
    }

    // Add new user
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'User added successfully!');
    }

    // Delete single user
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return back()->with('error', 'User not found!');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully!');
    }

    // Bulk delete users
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('user_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No users selected!');
        }

        User::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' user(s) deleted successfully!');
    }
}
