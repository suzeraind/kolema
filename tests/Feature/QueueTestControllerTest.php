<?php

use App\Jobs\LogRequestJob;
use Illuminate\Support\Facades\Queue;

test('queue test endpoint dispatches job with delay', function () {
    Queue::fake();

    $response = $this->getJson('/api/queue-test');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                'method',
                'url',
                'ip',
                'user_agent',
                'payload',
            ],
            'queued_at',
        ])
        ->assertJson([
            'message' => 'Job dispatched successfully',
            'data' => [
                'method' => 'GET',
            ],
        ]);

    Queue::assertPushed(LogRequestJob::class, function ($job) {
        return $job->delay !== null;
    });
});
