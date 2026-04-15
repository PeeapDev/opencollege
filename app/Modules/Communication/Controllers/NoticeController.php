<?php

namespace App\Modules\Communication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Communication\Models\Notice;
use App\Modules\Communication\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        $institutionId = auth()->user()->current_institution_id;
        $notices = Notice::where('institution_id', $institutionId)
            ->with('creator')
            ->latest()
            ->paginate(20);
        return view('communication::notices.index', compact('notices'));
    }

    public function create()
    {
        return view('communication::notices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|in:all,students,staff,faculty,department,program',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:publish_date',
        ]);

        Notice::create([
            'institution_id' => auth()->user()->current_institution_id,
            'title' => $request->title,
            'content' => $request->content,
            'audience' => $request->audience,
            'publish_date' => $request->publish_date,
            'expiry_date' => $request->expiry_date,
            'is_pinned' => $request->boolean('is_pinned'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('notices.index')->with('success', 'Notice published successfully.');
    }

    public function edit(Notice $notice)
    {
        return view('communication::notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|in:all,students,staff,faculty,department,program',
            'publish_date' => 'required|date',
        ]);

        $notice->update($request->only('title', 'content', 'audience', 'publish_date', 'expiry_date', 'is_pinned'));
        return redirect()->route('notices.index')->with('success', 'Notice updated.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }

    // Messaging
    public function inbox()
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->where('institution_id', auth()->user()->current_institution_id)
            ->with('sender')
            ->latest()
            ->paginate(20);
        return view('communication::messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::where('sender_id', auth()->id())
            ->where('institution_id', auth()->user()->current_institution_id)
            ->with('receiver')
            ->latest()
            ->paginate(20);
        return view('communication::messages.sent', compact('messages'));
    }

    public function compose()
    {
        $users = User::where('current_institution_id', auth()->user()->current_institution_id)
            ->where('id', '!=', auth()->id())
            ->orderBy('name')
            ->get();
        return view('communication::messages.compose', compact('users'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'institution_id' => auth()->user()->current_institution_id,
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.sent')->with('success', 'Message sent.');
    }

    public function readMessage(Message $message)
    {
        if ($message->receiver_id === auth()->id() && !$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }
        return view('communication::messages.read', compact('message'));
    }
}
