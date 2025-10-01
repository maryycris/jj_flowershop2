<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index()
    {
        // Show only staff accounts except admin
        $users = User::whereNotIn('role', ['customer', 'admin'])->get();
        $stores = Store::all();
        return view('admin.users.index', compact('users', 'stores'));
    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'sex' => 'required|string|in:M,F',
            'contact_number' => 'required|string|max:20',
            'role' => 'required|string|in:clerk,driver',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Add driver-specific validation if role is driver
        if ($request->role === 'driver') {
            $validationRules = array_merge($validationRules, [
                'license_number' => 'required|string|max:50|unique:drivers',
                'vehicle_type' => 'required|string|in:Motorcycle,Car,Van,Truck',
                'vehicle_plate' => 'required|string|max:20',
                'work_start_time' => 'required|date_format:H:i',
                'work_end_time' => 'required|date_format:H:i',
                'max_deliveries_per_day' => 'required|integer|min:1|max:50',
            ]);
        }

        $request->validate($validationRules);

        // Generate an internal email since the field was removed from the UI
        $emailBase = strtolower(preg_replace('/[^a-z0-9]+/i', '', $request->username)) ?: 'staff';
        $email = $emailBase . '@internal.local';
        while (User::where('email', $email)->exists()) {
            $email = $emailBase . '+' . Str::random(4) . '@internal.local';
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'sex' => $request->sex,
            'contact_number' => $request->contact_number,
            'role' => $request->role,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Create driver profile if role is driver
        if ($request->role === 'driver') {
            \App\Models\Driver::create([
                'user_id' => $user->id,
                'license_number' => $request->license_number,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate' => $request->vehicle_plate,
                'availability_status' => 'available',
                'work_start_time' => $request->work_start_time,
                'work_end_time' => $request->work_end_time,
                'max_deliveries_per_day' => $request->max_deliveries_per_day,
                'current_deliveries_today' => 0,
                'is_active' => true
            ]);
        }

        return Redirect::route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'required|string|in:M,F',
            'contact_number' => 'required|string|max:20',
            'role' => 'required|string|in:clerk,driver',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->name = $request->name;
        $user->sex = $request->sex;
        $user->contact_number = $request->contact_number;
        $user->role = $request->role;
        // No store_name field anymore
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