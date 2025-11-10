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
                return back()->withErrors(['login_field' => 'Please verify your email first.']);
            }
            $role = $user->role;
            return redirect()->route("$role.dashboard");
        }
        
        // Check if user exists but wrong password
        $userExists = \App\Models\User::where('email', $loginField)->orWhere('contact_number', $loginField)->first();
        if ($userExists) {
            return back()->withErrors(['password' => 'Incorrect password. Please try again.']);
        }
        
        return back()->withErrors(['login_field' => 'No account found with this email or phone number. Please register first.']);
    }

    public function showRegister() {
        $stores = Store::all();
        return view('auth.register', compact('stores'));
    }

    public function register(Request $request) {
        // Check if user already exists before validation
        $existingUser = \App\Models\User::where('email', $request->email)->first();
        if ($existingUser) {
            return back()->with('error', 'You already have an account with this email. Please login instead.')->withInput();
        }

        // Updated validation: only email is required now
        $request->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:/^\\S.*\\S$|^\\S$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^\\S.*\\S$|^\\S$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'first_name.regex' => 'First Name must not start or end with a space.',
            'last_name.regex' => 'Last Name must not start or end with a space.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $email = $request->input('email');
        $verificationChannel = 'email'; // Always use email for verification

        // Generate verification code and expiry
        $verificationCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        // Store registration data and code in session
        session([
            'pending_registration' => [
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => $email,
                'contact_number' => null, // No phone number required
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
                \Mail::send([], [], function ($message) use ($email, $verificationCode) {
                    $message->to($email)
                        ->subject('JJ Flowershop Registration Verification Code')
                        ->html("Your JJ Flowershop verification code is: <strong>$verificationCode</strong><br><br>This code will expire in 10 minutes.");
                });
            } catch (\Exception $e) {
                \Log::error('Email sending failed', ['error' => $e->getMessage(), 'email' => $email]);
                return back()->withErrors(['email' => 'Failed to send verification code to your email. Please check your email configuration or try again.'])->withInput();
            }
        } else if ($verificationChannel === 'phone' && $phone) {
            // REAL SMS sending via Semaphore
            \App\Helpers\SMSHelper::sendSMS($phone, $verificationCode);
            // Do not set or display demo code at all; send only via SMS now
            session()->forget('sms_demo_code');
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
            // Use the new app ID
            $clientId = '769015785952499';
            // For localhost, use hardcoded URL. For Railway/production, use APP_URL
            $appUrl = config('app.url');
            if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
                $redirectUri = 'http://localhost:8000/auth/facebook/callback';
            } else {
                $redirectUri = env('FACEBOOK_REDIRECT_URI', rtrim($appUrl, '/') . '/auth/facebook/callback');
            }
            $state = csrf_token();
            
            $url = "https://www.facebook.com/v18.0/dialog/oauth?" . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'state' => $state,
                'response_type' => 'code'
            ]);
            
            return redirect($url);
        }
        
        if ($provider === 'google') {
            // Check if Google credentials are configured
            $clientId = env('GOOGLE_CLIENT_ID', config('services.google.client_id'));
            if (empty($clientId)) {
                return redirect('/login')->withErrors(['message' => 'Google login is not configured. Please contact the administrator.']);
            }
            
            // For localhost, use default Socialite. For Railway/production, set redirect URL
            $appUrl = config('app.url');
            if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
                // Localhost - use default behavior (no redirectUrl set)
                return Socialite::driver('google')
                    ->scopes(['email', 'profile', 'https://www.googleapis.com/auth/user.phonenumbers.read'])
                    ->redirect();
            } else {
                // Railway/Production - set redirect URL explicitly
                $redirectUri = env('GOOGLE_REDIRECT_URI', rtrim($appUrl, '/') . '/auth/google/callback');
                return Socialite::driver('google')
                    ->redirectUrl($redirectUri)
                    ->scopes(['email', 'profile', 'https://www.googleapis.com/auth/user.phonenumbers.read'])
                    ->redirect();
            }
        }
        
        return Socialite::driver($provider)->redirect();
    }

    // Social Login: Handle provider callback
    public function handleProviderCallback($provider)
    {
        \Log::info('Socialite callback hit', ['provider' => $provider]);
        
        // Handle custom Facebook OAuth flow
        if ($provider === 'facebook') {
            return $this->handleFacebookCallback();
        }
        
        try {
            $socialUser = Socialite::driver($provider)->user();
            \Log::info('Socialite user', ['email' => $socialUser->getEmail()]);
        } catch (\Exception $e) {
            \Log::error('Socialite error', ['error' => $e->getMessage()]);
            return redirect('/login')->withErrors(['message' => 'Authentication failed or cancelled.']);
        }

        // Find existing user or create new one for social login
        $user = User::where('email', $socialUser->getEmail())->first();
        
        if (!$user) {
            // Split the full name into first and last name
            $fullName = $socialUser->getName();
            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            // Phone number not available from Google social login
            $phoneNumber = null;
            
            // Create new user for social login
            $user = new User([
                'name' => $fullName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $socialUser->getEmail(),
                'contact_number' => $phoneNumber,
                'profile_picture' => $socialUser->getAvatar(),
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
                'role' => 'customer',
                'is_verified' => true, // Auto-verify social login users
            ]);
            
            // Set social IDs
            if ($provider === 'google') {
                $user->google_id = $socialUser->getId();
            }
            if ($provider === 'facebook') {
                $user->facebook_id = $socialUser->getId();
            }
            
            $user->save();
            \Log::info('New user created via social login', ['email' => $socialUser->getEmail(), 'provider' => $provider]);
        } else {
            // Update social IDs if not set
            if ($provider === 'google' && !$user->google_id) {
                $user->google_id = $socialUser->getId();
            }
            if ($provider === 'facebook' && !$user->facebook_id) {
                $user->facebook_id = $socialUser->getId();
            }
            
            // Update first_name and last_name if they're empty
            if (empty($user->first_name) || empty($user->last_name)) {
                $fullName = $socialUser->getName();
                $nameParts = explode(' ', trim($fullName), 2);
                $user->first_name = $nameParts[0] ?? $user->first_name;
                $user->last_name = $nameParts[1] ?? $user->last_name;
            }
            
            // Update profile picture if user doesn't have one or if it's from social login
            if (empty($user->profile_picture) || !filter_var($user->profile_picture, FILTER_VALIDATE_URL)) {
                $user->profile_picture = $socialUser->getAvatar();
            }
            
            // Note: Social providers don't provide phone numbers by default
            // Users will need to add their phone number manually in their profile
            
            $user->save();
        }

        // Log in the user directly (no verification needed for social login)
        \Auth::login($user, true);
        
        $message = $user->wasRecentlyCreated ? 
            'Welcome to JJ Flowershop! Your account has been created and you are now logged in.' :
            'Welcome back! You are now logged in.';
        
        return redirect()->route('customer.dashboard')->with('success', $message);
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

    public function resendEmailCode()
    {
        $userId = session('pending_social_user_id');
        if (!$userId) {
            return redirect('/register')->with('error', 'No social login in progress.');
        }
        
        $user = User::find($userId);
        if (!$user) {
            return redirect('/register')->with('error', 'No social login in progress.');
        }
        
        // Generate new code
        $verificationCode = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        
        $user->verification_code = $verificationCode;
        $user->verification_expires_at = $expiresAt;
        $user->save();
        
        // Send email
        \Mail::send([], [], function ($message) use ($user, $verificationCode) {
            $message->to($user->email)
                ->subject('JJ Flowershop Verification Code (Resent)')
                ->html("Your JJ Flowershop verification code is: <strong>$verificationCode</strong><br><br>This code will expire in 10 minutes.");
        });
        
        return redirect()->route('social.verify.form')->with('success', 'Verification code has been resent to your email!');
    }
    
    public function resendSMSCode()
    {
        // For now, redirect to email resend since social profiles don't provide phone numbers
        return redirect()->route('social.resend.email')->with('info', 'Phone verification not available for social login. Resending to email instead.');
    }

    public function manualVerifyUser($userId)
    {
        // This method is for support team to manually verify users
        // In a real application, you'd want to add authentication/authorization here
        
        $user = User::find($userId);
        if (!$user) {
            return redirect('/register')->with('error', 'User not found.');
        }
        
        // Mark user as verified
        $user->is_verified = true;
        $user->verification_code = null;
        $user->verification_expires_at = null;
        $user->save();
        
        // Log in the user
        \Auth::login($user, true);
        session()->forget('pending_social_user_id');
        
        return redirect()->route('customer.dashboard')->with('success', 'Account manually verified by support team. Welcome!');
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

    // Custom Facebook callback handler
    private function handleFacebookCallback()
    {
        $request = request();
        
        if ($request->has('error')) {
            \Log::error('Facebook OAuth error', ['error' => $request->get('error')]);
            return redirect('/login')->withErrors(['message' => 'Facebook login was cancelled or failed.']);
        }

        if (!$request->has('code')) {
            \Log::error('Facebook OAuth missing code');
            return redirect('/login')->withErrors(['message' => 'Facebook login failed - no authorization code.']);
        }

        try {
            // Exchange code for access token
            $clientId = '769015785952499';
            $clientSecret = 'e3751172c5bf6451c8f2ed10656abfb0';
            // For localhost, use hardcoded URL. For Railway/production, use APP_URL
            $appUrl = config('app.url');
            if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
                $redirectUri = 'http://localhost:8000/auth/facebook/callback';
            } else {
                $redirectUri = env('FACEBOOK_REDIRECT_URI', rtrim($appUrl, '/') . '/auth/facebook/callback');
            }
            $code = $request->get('code');

            $tokenResponse = \Http::post('https://graph.facebook.com/v18.0/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Failed to get access token');
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];

            // Get user info from Facebook
            $userResponse = \Http::get("https://graph.facebook.com/v18.0/me", [
                'access_token' => $accessToken,
                'fields' => 'id,name,email,picture'
            ]);

            if (!$userResponse->successful()) {
                throw new \Exception('Failed to get user info');
            }

            $facebookUser = $userResponse->json();
            \Log::info('Facebook user data', ['user' => $facebookUser]);

            // Check if email is available
            if (!isset($facebookUser['email']) || empty($facebookUser['email'])) {
                // If no email, create a temporary one or use Facebook ID
                $email = $facebookUser['id'] . '@facebook.temp';
                \Log::warning('Facebook user has no email, using temporary email', ['facebook_id' => $facebookUser['id']]);
            } else {
                $email = $facebookUser['email'];
            }

            // Find or create user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                // Create new user
                $nameParts = explode(' ', trim($facebookUser['name']), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $user = new User([
                    'name' => $facebookUser['name'],
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'contact_number' => null,
                    'profile_picture' => $facebookUser['picture']['data']['url'] ?? null,
                    'password' => \Hash::make(\Str::random(24)),
                    'role' => 'customer',
                    'is_verified' => true,
                    'facebook_id' => $facebookUser['id'],
                ]);
                $user->save();
                \Log::info('New user created via Facebook login', ['email' => $email]);
            } else {
                // Update existing user with Facebook ID and profile picture
                if (!$user->facebook_id) {
                    $user->facebook_id = $facebookUser['id'];
                }
                
                // Update profile picture if user doesn't have one or if it's from social login
                if (empty($user->profile_picture) || !filter_var($user->profile_picture, FILTER_VALIDATE_URL)) {
                    $user->profile_picture = $facebookUser['picture']['data']['url'] ?? null;
                }
                
                $user->save();
            }

            // Log in the user
            \Auth::login($user, true);
            
            \Log::info('User logged in via Facebook', ['user_id' => $user->id]);
            return redirect()->route('customer.dashboard')->with('success', 'Successfully logged in with Facebook!');

        } catch (\Exception $e) {
            \Log::error('Facebook callback error', ['error' => $e->getMessage()]);
            return redirect('/login')->withErrors(['message' => 'Facebook login failed: ' . $e->getMessage()]);
        }
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
                \Mail::send([], [], function ($message) use ($pending, $verificationCode) {
                    $message->to($pending['email'])
                        ->subject('JJ Flowershop Registration Verification Code')
                        ->html("Your JJ Flowershop verification code is: <strong>$verificationCode</strong><br><br>This code will expire in 10 minutes.");
                });
            } catch (\Exception $e) {
                \Log::error('Email resend failed', ['error' => $e->getMessage(), 'email' => $pending['email']]);
                return back()->withErrors(['email' => 'Failed to resend verification code to your email. Please check your email configuration or try again.']);
            }
        } else if ($pending['verification_channel'] === 'phone' && $pending['contact_number']) {
            // REAL SMS sending via Semaphore
            \App\Helpers\SMSHelper::sendSMS($pending['contact_number'], $verificationCode);
            if (app()->environment('local')) {
                session(['sms_demo_code' => $verificationCode]);
            } else {
                session()->forget('sms_demo_code');
            }
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