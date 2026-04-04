<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminUserId = $request->session()->get('admin_user_id');

        if ($request->session()->get('admin_authenticated') && $adminUserId) {
            $adminUser = User::query()->whereKey($adminUserId)->where('is_admin', true)->first();

            if ($adminUser) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
