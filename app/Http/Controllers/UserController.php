<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'customer')->get(); // Only staff, not customers
        $stores = Store::all();
        return view('admin.users.index', compact('users', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'required|string|in:M,F',
            'contact_number' => 'required|string|max:20',
            'role' => 'required|string|in:admin,clerk,customer,driver',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'email' => 'required|email|unique:users,email',
            'store_name' => $request->role === 'admin' ? 'nullable' : 'required|string|in:Lapu-lapu,Cebu City,Cordova',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'sex' => $request->sex,
            'contact_number' => $request->contact_number,
            'role' => $request->role,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'store_name' => $request->store_name,
        ]);

        return Redirect::route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'required|string|in:M,F',
            'contact_number' => 'required|string|max:20',
            'role' => 'required|string|in:admin,clerk,customer,driver',
            'store_name' => $request->role === 'admin' ? 'nullable' : 'required|string|in:Lapu-lapu,Cebu City,Cordova',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->sex = $request->sex;
        $user->contact_number = $request->contact_number;
        $user->role = $request->role;
        $user->store_name = $request->store_name;
        $user->username = $request->username;
        $user->save();

        return Redirect::route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return Redirect::route('admin.users.index')->with('success', 'User deleted successfully!');
    }
} 