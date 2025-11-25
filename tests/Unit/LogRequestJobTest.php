<?php

use App\Jobs\LogRequestJob;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

uses(TestCase::class);

test('log request job logs request data', function () {
    Log::spy();

    $requestData = [
        'method' => 'POST',
        'url' => 'http://localhost/api/queue-test',
        'ip' => '127.0.0.1',
        'user_agent' => 'Test Agent',
        'payload' => ['test' => 'data'],
    ];

    $job = new LogRequestJob($requestData);
    $job->handle();

    Log::shouldHaveReceived('info')
        ->once()
        ->withArgs(function ($message, $context) use ($requestData) {
            return $message === 'Job executed: LogRequestJob'
                && $context['request_data'] === $requestData
                && isset($context['executed_at']);
        });
});

test('log request job can be instantiated with request data', function () {
    $requestData = [
        'method' => 'GET',
        'url' => 'http://example.com',
        'ip' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0',
        'payload' => [],
    ];

    $job = new LogRequestJob($requestData);

    expect($job->requestData)->toBe($requestData);
});
