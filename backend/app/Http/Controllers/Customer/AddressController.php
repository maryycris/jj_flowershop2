<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();
        return view('customer.address_book.index', compact('addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'is_default' => 'boolean',
            'municipality' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_default'] = $request->has('is_default') ? 1 : 0;
        // Fallbacks for databases that require non-null municipality
        if (empty($validated['municipality'])) {
            $validated['municipality'] = $validated['city'];
        }

        // If this is set as default, unset any existing default
        if ($validated['is_default']) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Address::create($validated);

        return redirect()->route('customer.address_book.index')
            ->with('success', 'Address added successfully.');
    }

    public function update(Request $request, Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'street_address' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'is_default' => 'boolean',
            'municipality' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        $validated['is_default'] = $request->has('is_default') ? 1 : 0;
        if (empty($validated['municipality'])) {
            $validated['municipality'] = $validated['city'];
        }

        // If this is set as default, unset any existing default
        if ($validated['is_default']) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('customer.address_book.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('customer.address_book.index')
            ->with('success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        // Ensure the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset any existing default
        Auth::user()->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->route('customer.address_book.index')
            ->with('success', 'Default address updated successfully.');
    }
}
