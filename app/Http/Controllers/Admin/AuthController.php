<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->syncConfiguredAdminUser();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('email', $credentials['email'])
            ->where('is_admin', true)
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Invalid administrative credentials.'])
                ->onlyInput('email');
        }

        $request->session()->put('admin_authenticated', true);
        $request->session()->put('admin_user_id', $user->id);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->forget('admin_user_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function syncConfiguredAdminUser(): void
    {
        $email = env('ADMIN_EMAIL', 'pes@dost.gov.ph');
        $passwordHash = env('ADMIN_PASSWORD_HASH');

        if (! $email || ! $passwordHash) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'PES Administrator',
                'password' => $passwordHash,
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
