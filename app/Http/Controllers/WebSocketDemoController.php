<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebSocketDemoController extends Controller
{
    /**
     * Display the WebSocket demo page.
     */
    public function index(): Response
    {
        $messages = Message::query()
            ->with('user:id,name')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (Message $message) => [
                'id' => $message->id,
                'message' => $message->message,
                'user' => $message->user->name,
                'userId' => $message->user_id,
                'timestamp' => $message->created_at->toIso8601String(),
            ]);

        return Inertia::render('WebSocketDemo', [
            'initialMessages' => $messages,
            'currentUser' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
            ],
        ]);
    }

    /**
     * Send a message via WebSocket.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $message = Message::create([
            'message' => $validated['message'],
            'user_id' => auth()->id(),
        ]);

        $message->load('user:id,name');

        event(new MessageSentEvent($message));

        return back();
    }

    /**
     * Delete a message.
     */
    public function destroy(Message $message): RedirectResponse
    {
        abort_if($message->user_id !== auth()->id(), 403, 'Unauthorized to delete this message');

        $messageId = $message->id;
        $message->delete();

        event(new \App\Events\MessageDeletedEvent($messageId));

        return back();
    }
}
