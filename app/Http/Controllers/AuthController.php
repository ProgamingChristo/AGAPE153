<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Models\WhatsAppVerificationCode;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:160'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($data['login']);
        $email = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? $login
            : User::query()->where('phone', $this->normalizePhone($login))->value('email');

        $successful = $email
            ? Auth::attempt(['email' => $email, 'password' => $data['password']], $request->boolean('remember'))
            : false;

        LoginHistory::query()->create([
            'user_id' => $successful ? $request->user()->id : null,
            'email' => $email ?: $login,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $successful,
        ]);

        if (! $successful) {
            return back()->withErrors(['login' => 'Email/WhatsApp atau password tidak sesuai.'])->onlyInput('login');
        }

        if ($request->user()->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('status', 'Please use the dedicated admin login page.');
        }

        $request->session()->regenerate();
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('member.dashboard'));
    }

    public function showAdminLogin(Request $request)
    {
        if ($request->user()?->isAdmin() && $request->session()->has('admin_session_token')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login', [
            'timeoutMinutes' => (int) config('session.admin_lifetime', 30),
        ]);
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $successful = Auth::attempt($credentials, false);

        LoginHistory::query()->create([
            'user_id' => $successful ? $request->user()->id : null,
            'email' => $credentials['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $successful,
        ]);

        if (! $successful) {
            return back()->withErrors(['email' => 'Admin email or password is incorrect.'])->onlyInput('email');
        }

        if (! $request->user()->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors(['email' => 'This login page is only for administrators.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'admin_session_token' => Str::random(64),
            'admin_authenticated_at' => now()->timestamp,
            'admin_last_activity' => time(),
        ]);
        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
        ]);

        $status = PasswordBroker::sendResetLink($data);

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:160'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = PasswordBroker::reset($data, function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.')
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        if (! empty($data['phone'])) {
            $data['phone'] = $this->normalizePhone($data['phone']);

            validator($data, [
                'phone' => ['nullable', Rule::unique('users', 'phone')],
            ])->validate();
        }

        $user = User::query()->create([
            ...$data,
            'auth_provider' => 'email',
            'role' => 'member',
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('status', 'Akun berhasil dibuat.');
    }

    public function registerWithWhatsApp(Request $request, NotificationService $notifications)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $phone = $this->normalizePhone($data['phone']);

        validator(['phone' => $phone], [
            'phone' => ['required', Rule::unique('users', 'phone')],
        ])->validate();

        WhatsAppVerificationCode::query()
            ->where('phone', $phone)
            ->where('purpose', 'register')
            ->whereNull('verified_at')
            ->delete();

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        WhatsAppVerificationCode::query()->create([
            'phone' => $phone,
            'code' => $code,
            'purpose' => 'register',
            'payload' => [
                'name' => $data['name'],
                'company_name' => $data['company_name'] ?? null,
                'password_hash' => Hash::make($data['password']),
            ],
            'expires_at' => $expiresAt,
        ]);

        $notifications->sendWhatsApp(
            'auth.whatsapp_otp',
            $phone,
            "Kode OTP Agape153 Anda adalah {$code}. Kode berlaku 10 menit dan jangan dibagikan.",
            [
                'phone' => $phone,
                'purpose' => 'register',
                'expires_at' => $expiresAt->toDateTimeString(),
            ],
        );

        return redirect()
            ->route('register.whatsapp.verify.form', ['phone' => $phone])
            ->with('status', 'Kode OTP sudah dikirim ke WhatsApp. Masukkan kode untuk mengaktifkan akun.');
    }

    public function showWhatsAppVerification(Request $request)
    {
        return view('auth.verify-whatsapp', [
            'phone' => $request->query('phone', old('phone')),
        ]);
    }

    public function verifyWhatsAppRegistration(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
            'code' => ['required', 'digits:6'],
        ]);

        $phone = $this->normalizePhone($data['phone']);

        $verification = WhatsAppVerificationCode::query()
            ->where('phone', $phone)
            ->where('purpose', 'register')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $verification) {
            return back()->withErrors(['code' => 'Kode OTP tidak ditemukan. Silakan kirim ulang OTP.'])->withInput(['phone' => $phone]);
        }

        if ($verification->expires_at->isPast()) {
            return back()->withErrors(['code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.'])->withInput(['phone' => $phone]);
        }

        if ($verification->attempts >= 5) {
            return back()->withErrors(['code' => 'Percobaan OTP terlalu banyak. Silakan kirim ulang OTP.'])->withInput(['phone' => $phone]);
        }

        $verification->increment('attempts');

        if (! hash_equals($verification->code, $data['code'])) {
            return back()->withErrors(['code' => 'Kode OTP tidak sesuai.'])->withInput(['phone' => $phone]);
        }

        validator(['phone' => $phone], [
            'phone' => ['required', Rule::unique('users', 'phone')],
        ])->validate();

        $payload = $verification->payload ?? [];
        $user = User::query()->create([
            'name' => $payload['name'] ?? 'Agape153 Buyer',
            'email' => $this->generatedEmailForPhone($phone),
            'phone' => $phone,
            'company_name' => $payload['company_name'] ?? null,
            'password' => $payload['password_hash'] ?? Str::random(48),
            'auth_provider' => 'whatsapp',
            'phone_verified_at' => now(),
            'role' => 'member',
            'status' => 'active',
        ]);

        $verification->update(['verified_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.dashboard')->with('status', 'Nomor WhatsApp berhasil diverifikasi dan akun aktif.');
    }

    public function resendWhatsAppOtp(Request $request, NotificationService $notifications)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $phone = $this->normalizePhone($data['phone']);
        $verification = WhatsAppVerificationCode::query()
            ->where('phone', $phone)
            ->where('purpose', 'register')
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $verification) {
            return redirect()->route('register')->withErrors(['phone' => 'Data registrasi WhatsApp tidak ditemukan. Silakan daftar ulang.']);
        }

        $code = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $verification->update([
            'code' => $code,
            'attempts' => 0,
            'expires_at' => $expiresAt,
        ]);

        $notifications->sendWhatsApp(
            'auth.whatsapp_otp_resend',
            $phone,
            "Kode OTP Agape153 Anda adalah {$code}. Kode berlaku 10 menit dan jangan dibagikan.",
            [
                'phone' => $phone,
                'purpose' => 'register',
                'expires_at' => $expiresAt->toDateTimeString(),
            ],
        );

        return redirect()
            ->route('register.whatsapp.verify.form', ['phone' => $phone])
            ->with('status', 'Kode OTP baru sudah dikirim ke WhatsApp.');
    }

    public function redirectToGoogle(Request $request)
    {
        $googleConfig = $this->googleOAuthConfig();

        if (! $googleConfig['client_id'] || ! $googleConfig['client_secret']) {
            return redirect($request->headers->get('referer') ?: route('login'))
                ->with('status', 'Google login belum aktif. Isi Google Client ID dan Client Secret dari Admin > Appearance > Integrations.');
        }

        config(['services.google' => $googleConfig]);

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        config(['services.google' => $this->googleOAuthConfig()]);

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['google' => 'Login Google gagal. Silakan coba lagi atau gunakan email/WhatsApp.']);
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->withErrors(['google' => 'Akun Google tidak memberikan alamat email.']);
        }

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'auth_provider' => $user->auth_provider === 'email' ? 'google' : $user->auth_provider,
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
                'status' => 'active',
            ]);
        } else {
            $user = User::query()->create([
                'name' => $googleUser->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'password' => Str::random(48),
                'auth_provider' => 'google',
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => 'member',
                'status' => 'active',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $user->update(['last_login_at' => now()]);

        if ($user->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->with('status', 'Please use the dedicated admin login page.');
        }

        return redirect()->intended(route('member.dashboard'))->with('status', 'Login Google berhasil.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function adminLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Admin session ended.');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?: '';

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return $phone;
    }

    private function generatedEmailForPhone(string $phone): string
    {
        $base = "wa.{$phone}@agape153.local";
        $email = $base;
        $index = 2;

        while (User::query()->where('email', $email)->exists()) {
            $email = "wa.{$phone}.{$index}@agape153.local";
            $index++;
        }

        return $email;
    }

    private function googleOAuthConfig(): array
    {
        return [
            'client_id' => WebsiteSetting::value('google_client_id') ?: config('services.google.client_id'),
            'client_secret' => WebsiteSetting::value('google_client_secret') ?: config('services.google.client_secret'),
            'redirect' => WebsiteSetting::value('google_redirect_uri') ?: config('services.google.redirect') ?: url('/auth/google/callback'),
        ];
    }
}
