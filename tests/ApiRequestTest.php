<?php

namespace Sppay\SpPayPhp\Tests;

use Sppay\SpPayPhp\SpPayApiRequest;

class ApiRequestTest extends BaseTestCase
{
    public function test_non_json_response_is_reported_not_thrown()
    {
        // example.com answers with an HTML page, which is what a firewall
        // challenge in front of the API looks like to the client.
        $response = (new SpPayApiRequest('https://example.com', '/', 'GET'))->sendRequest();

        $this->assertIsArray($response);
        $this->assertEquals('invalid_response', $response['error']);
        $this->assertEquals(200, $response['code']);
        $this->assertStringContainsString('text/html', $response['content_type']);
        $this->assertStringContainsStringIgnoringCase('<html', $response['body']);
    }
}
