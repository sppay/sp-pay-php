<?php

namespace Sppay\SpPayPhp\Tests;

use Sppay\SpPayPhp\SpPayPhp;

class EndpointTest extends BaseTestCase
{
    public function test_can_attempt_athentication()
    {
        $response = (new SpPayPhp())->generateAPIToken(
            'testId',
            'testSecret',
            'testUsername',
            'testPassword'
        );

        // Assert the response body
        $this->assertEquals([
            "code" => 401,
            "error" => "invalid_client",
            "error_description" => "Client authentication failed",
            "message" => "Client authentication failed",
        ], $response);
    }

    public function test_can_authenticate_successfully()
    {

        $response = (new SpPayPhp())->generateAPIToken(
            clientId: $_SERVER['TEST_CLIENT_ID'],
            clientSecret: $_SERVER['TEST_CLIENT_SECRET'],
            username: $_SERVER['TEST_USERNAME'],
            password: $_SERVER['TEST_PASSWORD']
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('Bearer', $response['token_type']);
        $this->assertEquals(31536000, $response['expires_in']);

        // Return bearer to be used in other requests.
        return $response['access_token'];
    }

    /**
     * @depends test_can_authenticate_successfully
     */
    public function test_can_initiate_payment($accessToken)
    {
        $response = (new SpPayPhp())->initiatePayment(
            bearerToken: $accessToken,
            recieveAccountNumber: '1000',
            amount: 2,
            payerEmail: 'michaelselbygh@gmail.com',
            payerAccountCode: 'SPP',
            payerAccountNumber: '1000',
            callbackUrl: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'status',
                'message',
                'object',
                'transaction',
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('transaction', $response['object']);
    }

    /**
     * @depends test_can_authenticate_successfully
     */
    public function test_can_validate_account($accessToken)
    {
        $response = (new SpPayPhp())->validateAccount(
            bearerToken: $accessToken,
            institutionCode: 'SPP',
            accountNumber: '1000'
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'status',
                'message',
                'object',
                'account',
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('account', $response['object']);
    }

    /**
     * @depends test_can_authenticate_successfully
     */
    // NOTE: Commented out for now. To be commented back in after fix is applied to validate transfer endpoint.
    public function test_can_validate_transfer($accessToken)
    {
        $response = (new SpPayPhp())->validateTransfer(
            bearerToken: $accessToken,
            customerReference: 'Test Via Package',
            sendAccountNumber: '1000',
            amount: 2,
            recipientAccountCode: 'SPP',
            recipientAccountNumber: '1001',
            callbackUrl: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'status',
                'message',
                'object',
                'transaction'
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('transaction', $response['object']);
    }

    /**
     * @depends test_can_authenticate_successfully
     */
    public function test_can_submit_transfer($accessToken)
    {
        $response = (new SpPayPhp())->submitTransfer(
            bearerToken: $accessToken,
            customerReference: 'Test Via Package',
            sendAccountNumber: '1000',
            amount: 2,
            recipientAccountCode: 'SPP',
            recipientAccountNumber: '1001',
            callbackUrl: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'status',
                'message',
                'object',
                'transaction',
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('transaction', $response['object']);
    }

    /**
     * @depends test_can_authenticate_successfully
     */
    public function test_can_send_sms($accessToken)
    {
        $response = (new SpPayPhp())->sendSMS(
            bearerToken: $accessToken,
            approvedSenderID: 'SP Pay',
            recipients: [
                "233271777557",
                "233597018036",
            ],
            message: 'Testing SMS from PHP Package'
        );

        // Assert response code.
        $this->assertEquals(200, $response['code']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'code',
                'status',
                'message',
                'object',
                'sms_campaign',
            ],
            $response
        );

        // Assert certain response values.
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('sms_campaign', $response['object']);
    }

    /** Helper function for asserting JSON Structures. */
    protected function assertJsonStructure(array $expectedStructure, array $responseArray)
    {
        foreach ($expectedStructure as $key => $value) {
            if (is_array($value)) {
                // Recursively check nested structure
                $this->assertArrayHasKey($key, $responseArray);
                $this->assertJsonStructure($value, $responseArray[$key]);
            } else {
                $this->assertArrayHasKey($value, $responseArray);
            }
        }
    }
}
