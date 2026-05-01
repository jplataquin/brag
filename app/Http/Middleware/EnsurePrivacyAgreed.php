<?php

namespace App\Http\Middleware;

use App\Models\PrivacyPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePrivacyAgreed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $latestPrivacy = PrivacyPolicy::latest('id')->first();
            
            if ($latestPrivacy && auth()->user()->privacy_version_agreed < $latestPrivacy->id) {
                // Prevent infinite loop if already on the privacy page
                if (!$request->routeIs('privacy.show') && !$request->routeIs('privacy.agree')) {
                    return redirect()->route('privacy.show');
                }
            }
        }

        return $next($request);
    }
}
