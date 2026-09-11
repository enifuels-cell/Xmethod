<?php

namespace App\Http\Controllers;

use App\Models\SupportCase;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SupportFlowController extends Controller
{
    public function landing()
    {
        return view('flow.1');
    }

    public function contact()
    {
        return view('flow.2');
    }

    public function storeContact(Request $request)
    {
        $contact = $request->validate([
            'email' => ['required', 'email'],
            'username' => ['required', 'string', 'max:20', 'regex:/^@?[A-Za-z0-9_]+$/'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $supportCase = SupportCase::create($contact);
        $request->session()->put([
            'contact' => $contact,
            'support_case_id' => $supportCase->id,
        ]);

        return redirect()->route('password.show');
    }

    public function password()
    {
        return view('flow.verify-password');
    }

    public function verifyPassword(Request $request)
    {
        $request->session()->put('password_verified', true);
        $this->updateCase($request, ['status' => 'identity_verified']);

        return redirect()->route('identity.show');
    }

    public function identity(Request $request)
    {
        abort_unless($request->session()->has('contact'), 403);

        return view('flow.3');
    }

    public function changeEmail(Request $request)
    {
        abort_unless($request->session()->get('password_verified'), 403);

        return view('flow.4');
    }

    public function storeEmail(Request $request)
    {
        $email = $request->validate([
            'email' => ['required', 'email'],
            'confirmed' => ['accepted'],
        ]);

        $request->session()->put('new_email', $email['email']);
        $this->updateCase($request, [
            'new_email' => $email['email'],
            'status' => 'email_change_requested',
        ]);

        return redirect()->route('code.show');
    }

    public function code(Request $request)
    {
        abort_unless($request->session()->has('new_email'), 403);

        return view('flow.5');
    }

    public function complete(Request $request)
    {
        abort_unless($request->session()->has('new_email'), 403);
        $request->session()->put('verification_complete', true);
        $this->updateCase($request, ['status' => 'verified']);

        return view('flow.6');
    }

    public function messages(Request $request)
    {
        abort_unless($request->session()->get('verification_complete'), 403);
        $supportCase = SupportCase::findOrFail($request->session()->get('support_case_id'));
        $messages = $supportCase->messages()->oldest()->get();

        return view('flow.7', compact('supportCase', 'messages'));
    }

    public function sendMessage(Request $request)
    {
        abort_unless($request->session()->get('verification_complete'), 403);
        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);
        $caseId = $request->session()->get('support_case_id');
        SupportCase::whereKey($caseId)->firstOrFail();
        SupportMessage::create([
            'support_case_id' => $caseId,
            'sender' => 'user',
            'body' => $data['body'],
        ]);
        $this->updateCase($request, ['status' => 'user_replied']);

        return redirect()->route('messages.show');
    }

    public function messageUpdates(Request $request)
    {
        abort_unless($request->session()->get('verification_complete'), 403);
        $supportCase = SupportCase::findOrFail($request->session()->get('support_case_id'));

        return response()->json($supportCase->messages()->oldest()->get()->map(fn (SupportMessage $message) => [
            'sender' => $message->sender,
            'body' => $message->body,
            'created_at' => $message->created_at->format('Y-m-d H:i'),
        ]));
    }

    public function setTyping(Request $request)
    {
        abort_unless($request->session()->get('verification_complete'), 403);
        $caseId = $request->session()->get('support_case_id');
        SupportCase::whereKey($caseId)->firstOrFail();
        Cache::put("support-case:{$caseId}:typing:user", true, now()->addSeconds(5));

        return response()->json(['ok' => true]);
    }

    public function typingStatus(Request $request)
    {
        abort_unless($request->session()->get('verification_complete'), 403);
        $caseId = $request->session()->get('support_case_id');

        return response()->json([
            'typing' => Cache::has("support-case:{$caseId}:typing:admin"),
        ]);
    }

    private function updateCase(Request $request, array $attributes): void
    {
        if ($caseId = $request->session()->get('support_case_id')) {
            SupportCase::whereKey($caseId)->update($attributes);
        }
    }
}
