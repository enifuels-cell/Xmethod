<?php

namespace App\Http\Controllers;

use App\Models\SupportCase;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function login()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate(['access_key' => ['required', 'string', 'max:255']]);
        $configuredKey = (string) config('app.admin_access_key');

        if ($configuredKey === '' || ! hash_equals($configuredKey, $request->string('access_key')->toString())) {
            return back()->withErrors(['access_key' => 'The admin access key is invalid.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        $cases = SupportCase::with(['messages' => fn ($query) => $query->oldest()])->latest()->get();

        return view('admin.dashboard', compact('cases'));
    }

    public function caseDetails(SupportCase $supportCase)
    {
        $supportCase->load(['messages' => fn ($query) => $query->oldest()]);

        return view('admin.case-details', compact('supportCase'));
    }

    public function generateInvite(Request $request)
    {
        $data = $request->validate([
            'hours' => ['required', 'integer', 'min:1', 'max:720'],
        ]);
        $expiresAt = now()->addHours((int) $data['hours']);
        $inviteUrl = URL::temporarySignedRoute(
            'invite.accept',
            $expiresAt,
            ['token' => Str::random(40)]
        );

        $cases = SupportCase::with(['messages' => fn ($query) => $query->oldest()])->latest()->get();

        return view('admin.dashboard', compact('inviteUrl', 'expiresAt', 'cases'));
    }

    public function sendMessage(Request $request, SupportCase $supportCase)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);
        SupportMessage::create([
            'support_case_id' => $supportCase->id,
            'sender' => 'admin',
            'body' => $data['body'],
        ]);
        $supportCase->update(['status' => 'admin_replied']);

        return redirect()->route('admin.cases.show', $supportCase);
    }

    public function endSession(SupportCase $supportCase)
    {
        $supportCase->delete();

        return redirect()->route('admin.dashboard');
    }

    public function caseMessages(SupportCase $supportCase)
    {
        return response()->json($supportCase->messages()->oldest()->get()->map(fn (SupportMessage $message) => [
            'sender' => $message->sender,
            'body' => $message->body,
            'created_at' => $message->created_at->format('Y-m-d H:i'),
        ]));
    }

    public function setTyping(Request $request, SupportCase $supportCase)
    {
        Cache::put("support-case:{$supportCase->id}:typing:admin", true, now()->addSeconds(5));

        return response()->json(['ok' => true]);
    }

    public function typingStatus(SupportCase $supportCase)
    {
        return response()->json([
            'typing' => Cache::has("support-case:{$supportCase->id}:typing:user"),
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('admin_authenticated');
        $request->session()->regenerate();

        return redirect()->route('admin.login');
    }
}
