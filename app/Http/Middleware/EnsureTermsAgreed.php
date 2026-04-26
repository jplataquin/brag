<?php

namespace App\Http\Middleware;

use App\Models\TermsOfService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTermsAgreed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $latestTerms = TermsOfService::latest('id')->first();
            
            if ($latestTerms && auth()->user()->terms_version_agreed < $latestTerms->id) {
                // Prevent infinite loop if already on the terms page
                if (!$request->routeIs('terms.show') && !$request->routeIs('terms.agree')) {
                    return redirect()->route('terms.show');
                }
            }
        }

        return $next($request);
    }
}
