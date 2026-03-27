<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $credentials = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $configuredPassword = env('ADMIN_PASSWORD', 'admin123');
        $configuredHash = env('ADMIN_PASSWORD_HASH');

        $valid = $configuredHash
            ? Hash::check($credentials['password'], $configuredHash)
            : hash_equals($configuredPassword, $credentials['password']);

        if (! $valid) {
            return back()->withErrors(['password' => 'Invalid administrative password.'])->onlyInput('password');
        }

        $request->session()->put('admin_authenticated', true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
