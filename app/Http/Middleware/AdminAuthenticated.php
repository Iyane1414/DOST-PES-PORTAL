<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminUserId = $request->session()->get('admin_user_id');
        $isAuthenticated = $request->session()->get('admin_authenticated');

        if (! $isAuthenticated || ! $adminUserId) {
            return redirect()->route('admin.login');
        }

        $adminUser = User::query()->whereKey($adminUserId)->where('is_admin', true)->first();

        if (! $adminUser) {
            $request->session()->forget(['admin_authenticated', 'admin_user_id']);

            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
