<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
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
        return Inertia::render('WebSocketDemo');
    }

    /**
     * Send a message via WebSocket.
     */
    public function sendMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'user' => ['nullable', 'string', 'max:100'],
        ]);

        event(new MessageSentEvent(
            message: $validated['message'],
            user: $validated['user'] ?? 'Anonymous'
        ));

        return back();
    }
}
