<?php

namespace Sppay\SpPayPhp\Tests;

use PHPUnit\Framework\TestCase;
use Sppay\SpPayPhp\SpPayPhp;

class AuthenticationAttemptTest extends TestCase
{
    public function test_can_attempt_athentication()
    {
        $response = (new SpPayPhp())->generateAPIToken(
            'testId',
            'testSecret',
            'testUsername',
            'testPassword'
        )->sendRequest();

        // Assert the response body
        $this->assertEquals([
            "error" => "invalid_client",
            "error_description" => "Client authentication failed",
            "message" => "Client authentication failed",
        ], json_decode($response, true));
    }
}
