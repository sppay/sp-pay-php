<?php

namespace Sppay\SpPayPhp\Tests;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Sppay\SpPayPhp\SpPayPhp;

#[Group('staging')]
class EndpointTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (empty($_SERVER['TEST_CLIENT_ID'])) {
            $this->markTestSkipped('Set TEST_CLIENT_ID, TEST_CLIENT_SECRET, TEST_USERNAME and TEST_PASSWORD to run the staging tests.');
        }
    }

    public function test_can_attempt_athentication()
    {
        $response = (new SpPayPhp(environment: 'staging'))->generateAPIToken(
            'testId',
            'testSecret',
            'testUsername',
            'testPassword'
        );

        // Assert the response body
        $this->assertEquals([
            "http_status" => 401,
            "error" => "invalid_client",
            "error_description" => "Client authentication failed",
            "message" => "Client authentication failed",
        ], $response);
    }

    public function test_can_authenticate_successfully()
    {

        $response = (new SpPayPhp(environment: 'staging'))->generateAPIToken(
            client_id: $_SERVER['TEST_CLIENT_ID'],
            client_secret: $_SERVER['TEST_CLIENT_SECRET'],
            username: $_SERVER['TEST_USERNAME'],
            password: $_SERVER['TEST_PASSWORD']
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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

    #[Depends('test_can_authenticate_successfully')]
    public function test_can_initiate_payment($access_token)
    {
        $response = (new SpPayPhp(environment: 'staging'))->initiatePayment(
            bearer_token: $access_token,
            recieve_account_number: '1000',
            amount: 2,
            payer_email: 'michaelselbygh@gmail.com',
            payer_account_code: 'SPP',
            payer_account_number: '1000',
            callback_url: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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

    #[Depends('test_can_authenticate_successfully')]
    public function test_can_validate_account($access_token)
    {
        $response = (new SpPayPhp(environment: 'staging'))->validateAccount(
            bearer_token: $access_token,
            institution_code: 'SPP',
            account_number: '1000'
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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

    #[Depends('test_can_authenticate_successfully')]
    public function test_can_validate_transfer($access_token)
    {
        $response = (new SpPayPhp(environment: 'staging'))->validateTransfer(
            bearer_token: $access_token,
            customer_reference: 'Test Via Package',
            send_account_number: '1000',
            amount: 2,
            recipient_account_code: 'SPP',
            recipient_account_number: '1001',
            callback_url: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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

    #[Depends('test_can_authenticate_successfully')]
    public function test_can_submit_transfer($access_token)
    {
        $response = (new SpPayPhp(environment: 'staging'))->submitTransfer(
            bearer_token: $access_token,
            customer_reference: 'Test Via Package',
            send_account_number: '1000',
            amount: 2,
            recipient_account_code: 'SPP',
            recipient_account_number: '1001',
            callback_url: 'https://callback.test.com'
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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

    #[Depends('test_can_authenticate_successfully')]
    public function test_can_send_sms($access_token)
    {
        $response = (new SpPayPhp(environment: 'staging'))->sendSMS(
            bearer_token: $access_token,
            approved_sender_id: 'SP Pay',
            recipients: [
                "233271777557",
                "233597018036",
            ],
            message: 'Testing SMS from PHP Package'
        );

        // Assert response code.
        $this->assertEquals(200, $response['http_status']);

        // Assert response structure.
        $this->assertJsonStructure(
            [
                'http_status',
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
    protected function assertJsonStructure(array $expected_structure, array $response_array)
    {
        foreach ($expected_structure as $key => $value) {
            if (is_array($value)) {
                // Recursively check nested structure
                $this->assertArrayHasKey($key, $response_array);
                $this->assertJsonStructure($value, $response_array[$key]);
            } else {
                $this->assertArrayHasKey($value, $response_array);
            }
        }
    }
}
