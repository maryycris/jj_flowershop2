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
                'license_number' => [
                    'required',
                    'string',
                    'max:15',
                    'regex:/^[A-Z0-9\-]+$/',
                    'unique:drivers'
                ],
                'vehicle_type' => 'required|string|in:Motorcycle,Car,Van,Truck',
                'vehicle_plate' => [
                    'required',
                    'string',
                    'max:8',
                    function ($attribute, $value, $fail) use ($request) {
                        $vehicleType = $request->vehicle_type;
                        $value = strtoupper(trim($value));
                        
                        if ($vehicleType === 'Car' || $vehicleType === 'Van' || $vehicleType === 'Truck') {
                            // Car (new): 3 letters + space + 4 digits = 8 characters
                            if (!preg_match('/^[A-Z]{3} [0-9]{4}$/', $value)) {
                                $fail('The vehicle plate number must be in format: 3 letters, space, 4 digits (e.g., ABC 1234).');
                            }
                        } elseif ($vehicleType === 'Motorcycle') {
                            // Motorcycle: 2 letters + space + 4 or 5 digits
                            if (!preg_match('/^[A-Z]{2} [0-9]{4,5}$/', $value)) {
                                $fail('The vehicle plate number must be in format: 2 letters, space, 4-5 digits (e.g., AB 1234 or AB 12345).');
                            }
                        }
                    },
                ],
                'work_start_time' => 'required|date_format:H:i',
                'work_end_time' => 'required|date_format:H:i',
                'max_deliveries_per_day' => 'required|integer|min:1|max:50',
            ]);
        }

        $validated = $request->validate($validationRules);
        
        // Normalize plate number and license number to uppercase before saving
        if ($request->role === 'driver') {
            if (isset($validated['vehicle_plate'])) {
                $validated['vehicle_plate'] = strtoupper(trim($validated['vehicle_plate']));
            }
            if (isset($validated['license_number'])) {
                $validated['license_number'] = strtoupper(trim($validated['license_number']));
            }
        }

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
                'license_number' => $validated['license_number'],
                'vehicle_type' => $validated['vehicle_type'],
                'vehicle_plate' => $validated['vehicle_plate'],
                'availability_status' => 'available',
                'work_start_time' => $validated['work_start_time'],
                'work_end_time' => $validated['work_end_time'],
                'max_deliveries_per_day' => $validated['max_deliveries_per_day'],
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