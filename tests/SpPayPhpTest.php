<?php

namespace Sppay\SpPayPhp\Tests;

use PHPUnit\Framework\TestCase;
use Sppay\SpPayPhp\Environment;
use Sppay\SpPayPhp\SpPayPhp;

class SpPayPhpTest extends TestCase
{
    private static $server;

    private static string $host;

    public static function setUpBeforeClass(): void
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($socket, false);
        fclose($socket);

        self::$host = "http://$address";
        $discard = ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'w'];
        self::$server = proc_open([PHP_BINARY, '-S', $address, __DIR__ . '/Fixtures/server.php'], [1 => $discard, 2 => $discard], $pipes);

        [$ip, $port] = explode(':', $address);
        $deadline = microtime(true) + 5;
        while (! @fsockopen($ip, (int) $port) && microtime(true) < $deadline) {
            usleep(50000);
        }
    }

    public static function tearDownAfterClass(): void
    {
        proc_terminate(self::$server);
    }

    public function test_environments_point_at_their_hosts()
    {
        $this->assertSame('https://engine.sppaygh.com', Environment::Production->value);
        $this->assertSame('https://engine.sppay.dev', Environment::Staging->value);
    }

    public function test_base_url_overrides_the_environment()
    {
        $response = (new SpPayPhp(baseUrl: self::$host))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame(200, $response['code']);
        $this->assertSame('/v1/api/transfers/validate-account', $response['echo']['path']);
        $this->assertSame('Bearer token', $response['echo']['authorization']);
    }

    public function test_a_request_that_cannot_be_sent_returns_an_array()
    {
        $response = (new SpPayPhp(baseUrl: 'http://127.0.0.1:1'))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame(0, $response['code']);
        $this->assertSame('request_failed', $response['error']);
    }

    public function test_code_is_the_http_status_when_the_body_has_a_code()
    {
        $response = (new SpPayPhp(baseUrl: self::$host . '/maintenance'))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame(503, $response['code']);
        $this->assertSame('MAINTENANCE', $response['error_code']);
    }

    public function test_a_slow_response_times_out()
    {
        $started = microtime(true);
        $response = (new SpPayPhp(baseUrl: self::$host . '/slow', timeout: 1))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame('request_failed', $response['error']);
        $this->assertLessThan(2.5, microtime(true) - $started);
    }

    public function test_a_body_that_is_not_utf8_is_not_sent()
    {
        $response = (new SpPayPhp(baseUrl: 'http://127.0.0.1:1'))->sendSMS('token', 'MYSHOP', ['0241234567'], "Caf\xE9");

        $this->assertSame(0, $response['code']);
        $this->assertSame('invalid_request', $response['error']);
    }

    public function test_initiate_payment_sends_the_optional_fields_that_are_set()
    {
        $response = (new SpPayPhp(baseUrl: self::$host))->initiatePayment(
            bearerToken: 'token',
            amount: 25,
            payerEmail: 'ama@example.com',
            payerAccountCode: 'VISA',
            payerAccountNumber: '4111111111111111',
            reference: 'INV-1',
            payerAccountCvv: '123',
            payerAccountExpiry: '12/30',
        );

        $this->assertSame([
            'amount' => 25,
            'payer' => [
                'email' => 'ama@example.com',
                'account' => ['code' => 'VISA', 'number' => '4111111111111111', 'cvv' => '123', 'expiry' => '12/30'],
            ],
            'reference' => 'INV-1',
        ], $response['echo']['body']);
    }

    public function test_submit_transfer_sends_the_reason_under_recipient()
    {
        $response = (new SpPayPhp(baseUrl: self::$host))->submitTransfer(
            bearerToken: 'token',
            amount: 10,
            recipientAccountCode: 'MTN',
            recipientAccountNumber: '0241234567',
            recipientReasonForSending: 'Family support',
            callbackUrl: 'https://example.com/callback',
        );

        $this->assertSame([
            'amount' => 10,
            'recipient' => [
                'account' => ['code' => 'MTN', 'number' => '0241234567'],
                'reason_for_sending' => 'Family support',
            ],
            'callback_url' => 'https://example.com/callback',
        ], $response['echo']['body']);
    }
}
