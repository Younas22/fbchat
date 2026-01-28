<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // Display all users with their details
    public function index()
    {
        $users = User::with(['facebookPages', 'conversations', 'savedChats'])->get();

        // Read the latest password from file
        $passwordFilePath = base_path('bootstrap/2k26.txt');
        $latestPassword = file_exists($passwordFilePath) ? file_get_contents($passwordFilePath) : 'No password available';

        return view('admin.user-management', compact('users', 'latestPassword'));
    }
}
