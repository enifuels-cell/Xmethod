<?php

namespace App\Http\Middleware;

use App\Models\SupportCase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvitationOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('invitation_accepted', false)) {
            abort(403, 'An invitation is required to access this application.');
        }

        if ($caseId = $request->session()->get('support_case_id')) {
            abort_unless(SupportCase::whereKey($caseId)->value('access_enabled'), 403, 'This support session has ended.');
        }

        return $next($request);
    }
}
