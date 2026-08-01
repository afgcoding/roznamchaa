<?php

namespace App\Http\Middleware;

use App\Models\Store;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreIsActive
{
    public const MESSAGE = 'This store account has been deactivated due to pending subscription fees. Please contact support.';

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Store) {
            return $next($request);
        }

        if ($tenant->is_active) {
            return $next($request);
        }

        /** @var User|null $user */
        $user = $request->user();

        // Super Admin may still open inactive stores for support / management.
        if ($user?->isSuperAdmin()) {
            return $next($request);
        }

        return response()->view('errors.store-deactivated', [
            'message' => self::MESSAGE,
            'store' => $tenant,
        ], 403);
    }
}
