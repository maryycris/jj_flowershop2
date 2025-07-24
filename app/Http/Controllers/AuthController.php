<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Models\Store;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.customer_login');
    }

    public function login(Request $request) {
        $request->validate([
            'login_field' => 'required',
            'password' => 'required',
        ]);
        $loginField = $request->input('login_field');
        $password = $request->input('password');

        // Determine if input is email or phone number
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $loginField, 'password' => $password];
        } elseif (preg_match('/^09\d{9}$/', $loginField)) {
            $credentials = ['contact_number' => $loginField, 'password' => $password];
        } else {
            return back()->withErrors(['login_field' => 'Please enter a valid email address or phone number.']);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->is_verified) {
                Auth::logout();
                return back()->withErrors(['email' => 'Please verify your email first.']);
            }
            $role = $user->role;
            return redirect()->route("$role.dashboard");
        }
        return back()->withErrors(['Invalid credentials']);
    }

    public function showRegister() {
        $stores = Store::all();
        return view('auth.register', compact('stores'));
    }

    public function register(Request $request) {
        // Custom validation: at least one of email or contact_number is required
        $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^\\S.*\\S$|^\\S$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^\\S.*\\S$|^\\S$/'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'first_name.regex' => 'First Name must not start or end with a space.',
            'last_name.regex' => 'Last Name must not start or end with a space.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $email = $request->input('email');
        $phone = $request->input('contact_number');
        $verificationChannel = $request->input('verification_channel');

        // At least one required
        if (empty($email) && empty($phone)) {
            return back()->withErrors(['at_least_one' => 'Please provide at least a Gmail or a phone number.'])->withInput();
        }

        // If both are provided, require channel selection
        if ($email && $phone && !$verificationChannel) {
            return back()->withErrors(['verification_channel' => 'Please select where you want to receive your verification code.'])->withInput();
        }

        // If only one is provided, set channel automatically
        if (!$verificationChannel) {
            $verificationChannel = $email ? 'email' : 'phone';
        }

        // Validate uniqueness for whichever is provided
        if ($email) {
            $request->validate([
                'email' => ['email', 'max:255', 'unique:users,email'],
            ]);
        }
        if ($phone) {
            $request->validate([
                'contact_number' => ['digits:11', 'unique:users,contact_number'],
            ], [
                'contact_number.digits' => 'Phone Number must be exactly 11 digits.',
            ]);
        }

        // Generate verification code and expiry
        $verificationCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        // Store registration data and code in session
        session([
            'pending_registration' => [
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => $email,
                'contact_number' => $phone,
                'password' => bcrypt($request->password),
                'verification_channel' => $verificationChannel,
                'verification_code' => $verificationCode,
                'verification_expires_at' => $expiresAt,
            ]
        ]);

        // Send code to selected channel
        if ($verificationChannel === 'email' && $email) {
            // Send code via email
            try {
                Mail::raw("Your JJ Flowershop verification code is: $verificationCode", function ($message) use ($email) {
                    $message->to($email)
                        ->subject('JJ Flowershop Registration Verification Code');
                });
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Failed to send verification code to your email. Please try again.'])->withInput();
            }
        } else if ($verificationChannel === 'phone' && $phone) {
            // For now, just simulate SMS sending (integration needed for real SMS)
            // You can implement SMS sending here using a provider like Twilio or Semaphore
            // For demo, we'll just flash the code (not for production)
            session(['sms_demo_code' => $verificationCode]);
        }

        // Redirect to verification page
        return redirect()->route('verify.code')->with('success', 'A verification code has been sent to your ' . ($verificationChannel === 'email' ? 'Gmail' : 'phone number') . '. Please enter the code to complete your registration.');
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    // Social Login: Redirect to provider
    public function redirectToProvider($provider)
    {
        $allowedProviders = ['google', 'facebook'];
        if (!in_array($provider, $allowedProviders)) {
            abort(404);
        }
        if ($provider === 'facebook') {
            return Socialite::driver('facebook')->with(['auth_type' => 'reauthenticate'])->redirect();
        }
        return Socialite::driver($provider)->redirect();
    }

    // Social Login: Handle provider callback
    public function handleProviderCallback($provider)
    {
        \Log::info('Socialite callback hit', ['provider' => $provider]);
        try {
            $socialUser = Socialite::driver($provider)->user();
            \Log::info('Socialite user', ['email' => $socialUser->getEmail()]);
        } catch (\Exception $e) {
            \Log::error('Socialite error', ['error' => $e->getMessage()]);
            return redirect('/login')->withErrors(['message' => 'Authentication failed or cancelled.']);
        }

        // Generate verification code
        $verificationCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        // Find or create user, but do NOT mark as verified yet
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'profile_picture' => $socialUser->getAvatar(),
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                'role' => 'customer',
                'google_id' => $provider === 'google' ? $socialUser->getId() : null,
                'facebook_id' => $provider === 'facebook' ? $socialUser->getId() : null,
                'is_verified' => false,
            ]
        );
        // If user exists but doesn't have the social ID, update it
        if ($provider === 'google' && !$user->google_id) {
            $user->google_id = $socialUser->getId();
        }
        if ($provider === 'facebook' && !$user->facebook_id) {
            $user->facebook_id = $socialUser->getId();
        }
        // Save verification code and expiry to user
        $user->verification_code = $verificationCode;
        $user->verification_expires_at = $expiresAt;
        $user->save();

        // Send code to email (Mailtrap)
        \Mail::raw("Your JJ Flowershop verification code is: $verificationCode", function ($message) use ($socialUser) {
            $message->to($socialUser->getEmail())
                ->subject('JJ Flowershop Social Login Verification Code');
        });

        // Store user id in session for verification
        session(['pending_social_user_id' => $user->id]);

        // Redirect to verification form
        return redirect()->route('social.verify.form')->with('success', 'A verification code has been sent to your email. Please enter the code to continue.');
    }

    public function showSocialVerifyForm() {
        return view('auth.social_verify');
    }

    public function verifySocialCode(Request $request) {
        $request->validate(['verification_code' => 'required|digits:6']);
        $userId = session('pending_social_user_id');
        if (!$userId) {
            return redirect('/login')->withErrors(['expired' => 'No social login in progress.']);
        }
        $user = User::find($userId);
        if (!$user || !$user->verification_code || !$user->verification_expires_at) {
            return redirect('/login')->withErrors(['expired' => 'No social login in progress.']);
        }
        if (now()->gt($user->verification_expires_at)) {
            return redirect()->route('social.verify.form')->withErrors(['verification_code' => 'Verification code expired.']);
        }
        if ($request->verification_code != $user->verification_code) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
        // Mark user as verified, clear code and expiry
        $user->is_verified = true;
        $user->verification_code = null;
        $user->verification_expires_at = null;
        $user->save();
        // Log in the user
        \Auth::login($user, true);
        session()->forget('pending_social_user_id');
        return redirect()->route('customer.dashboard')->with('success', 'Logged in via Social Login!');
    }

    // Show the phone verification form after Facebook login
    public function showFacebookPhoneForm()
    {
        if (!session()->has('pending_facebook_user_id')) {
            return redirect('/login')->withErrors(['expired' => 'No Facebook login in progress.']);
        }
        return view('auth.facebook_phone_verify');
    }

    // Handle phone verification after Facebook login
    public function verifyFacebookPhone(Request $request)
    {
        $request->validate(['phone' => 'required']);
        $userId = session('pending_facebook_user_id');
        $user = User::find($userId);
        if (!$user) {
            return redirect('/login')->withErrors(['expired' => 'No Facebook login in progress.']);
        }
        // Save phone and mark as verified
        $user->phone = $request->phone;
        $user->is_phone_verified = true;
        $user->save();
        \Auth::login($user, true);
        session()->forget('pending_facebook_user_id');
        return redirect()->route('customer.dashboard')->with('success', 'Phone verified and logged in!');
    }

    // Password Reset: Show request form
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Password Reset: Send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // Password Reset: Show reset form
    public function showResetForm($token)
    {
        $email = request('email');
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    // Password Reset: Handle reset
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function showVerificationForm() {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['expired' => 'No registration in progress. Please register again.']);
        }
        $pending = session('pending_registration');
        // Check expiry
        if (isset($pending['verification_expires_at']) && now()->gt($pending['verification_expires_at'])) {
            return view('auth.verify_code', ['expired' => true]);
        }
        return view('auth.verify_code', ['expired' => false]);
    }

    public function verifyCode(Request $request) {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['expired' => 'No registration in progress. Please register again.']);
        }
        $pending = session('pending_registration');
        // Check expiry
        if (isset($pending['verification_expires_at']) && now()->gt($pending['verification_expires_at'])) {
            return redirect()->route('verify.code')->withErrors(['verification_code' => 'Verification code expired. Please resend a new code.']);
        }
        $request->validate([
            'verification_code' => ['required', 'digits:6'],
        ]);
        if ($request->verification_code != $pending['verification_code']) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.'])->withInput();
        }
        // Create the user
        // Try to split the address into parts
        $street = $barangay = $municipality = $city = null;
        if (!empty($pending['address'])) {
            $parts = array_map('trim', explode(',', $pending['address']));
            $street = $parts[0] ?? null;
            $barangay = $parts[1] ?? null;
            $municipality = $parts[2] ?? null;
            $city = $parts[3] ?? null;
        }
        $user = User::create([
            'first_name' => $pending['first_name'],
            'last_name' => $pending['last_name'],
            'name' => $pending['first_name'] . ' ' . $pending['last_name'], // Add full name
            'email' => $pending['email'],
            'contact_number' => $pending['contact_number'],
            'password' => $pending['password'],
            'role' => 'customer',
            'is_verified' => 1, // Mark as verified after code verification
        ]);
        // Clear session
        session()->forget('pending_registration');
        session()->forget('sms_demo_code');
        // Log in the user
        Auth::login($user);
        // After login/registration, check if address fields are empty and flash a reminder
        if (empty($user->street_address) || empty($user->barangay) || empty($user->municipality) || empty($user->city)) {
            session()->flash('reminder', 'Please complete your profile and add your address.');
        }
        return redirect()->route('customer.dashboard')->with('success', 'Registration complete! Welcome to JJ Flowershop.');
    }

    public function resendCode() {
        if (!session()->has('pending_registration')) {
            return redirect()->route('register')->withErrors(['expired' => 'No registration in progress. Please register again.']);
        }
        $pending = session('pending_registration');
        $verificationCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        // Update session
        $pending['verification_code'] = $verificationCode;
        $pending['verification_expires_at'] = $expiresAt;
        session(['pending_registration' => $pending]);
        // Resend code
        if ($pending['verification_channel'] === 'email' && $pending['email']) {
            try {
                Mail::raw("Your JJ Flowershop verification code is: $verificationCode", function ($message) use ($pending) {
                    $message->to($pending['email'])
                        ->subject('JJ Flowershop Registration Verification Code');
                });
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'Failed to resend verification code to your email. Please try again.']);
            }
        } else if ($pending['verification_channel'] === 'phone' && $pending['contact_number']) {
            // Simulate SMS for demo
            session(['sms_demo_code' => $verificationCode]);
        }
        return redirect()->route('verify.code')->with('success', 'A new verification code has been sent.');
    }

    public function showCustomerLogin() {
        return view('auth.customer_login');
    }

    public function customerLogin(Request $request) {
        $request->validate([
            'login_field' => 'required',
            'password' => 'required',
        ]);
        $loginField = $request->input('login_field');
        $password = $request->input('password');

        // Determine if input is email or phone number
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $loginField)->first();
        } elseif (preg_match('/^09\\d{9}$/', $loginField)) {
            $user = \App\Models\User::where('contact_number', $loginField)->first();
        } else {
            return back()->withErrors(['login_field' => 'Please enter a valid email address or phone number.']);
        }

        if (!$user) {
            return back()->withErrors(['login_field' => 'No user found with that email or phone number.']);
        }
        if (!\Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }
        if ($user->role !== 'customer') {
            return back()->withErrors(['login_field' => 'Only customers can log in here.']);
        }
        if (!$user->is_verified) {
            return back()->withErrors(['email' => 'Please verify your email first.']);
        }
        \Auth::login($user);
        return redirect()->route('customer.dashboard');
    }

    public function showStaffLogin() {
        return view('auth.staff_login');
    }

    public function staffLogin(Request $request) {
        $request->validate([
            'login_field' => 'required',
            'password' => 'required',
        ]);
        $loginField = $request->input('login_field');
        $password = $request->input('password');

        // Only allow username login for staff
        $credentials = ['username' => $loginField, 'password' => $password];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!in_array($user->role, ['admin', 'clerk', 'driver'])) {
                Auth::logout();
                return back()->withErrors(['login_field' => 'Only staff (admin, clerk, driver) can log in here.']);
            }
            return redirect()->route($user->role . '.dashboard');
        }
        return back()->withErrors(['Invalid credentials']);
    }
} 