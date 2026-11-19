<?php

namespace Sppay\SpPayPhp\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
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

    public function test_an_unknown_environment_is_rejected()
    {
        $this->expectException(InvalidArgumentException::class);

        new SpPayPhp(environment: 'live');
    }

    public function test_base_url_overrides_the_environment()
    {
        $response = (new SpPayPhp(environment: 'staging', base_url: self::$host))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame(200, $response['http_status']);
        $this->assertSame('/v1/api/transfers/validate-account', $response['echo']['path']);
        $this->assertSame('Bearer token', $response['echo']['authorization']);
    }

    public function test_the_body_is_returned_unchanged_with_the_http_status()
    {
        $response = (new SpPayPhp(base_url: self::$host . '/maintenance'))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame([
            'http_status' => 503,
            'status' => 'error',
            'message' => 'SPPay is undergoing maintenance.',
            'code' => 'MAINTENANCE',
        ], $response);
    }

    public function test_a_request_that_cannot_be_sent_returns_an_error()
    {
        $response = (new SpPayPhp(base_url: 'http://127.0.0.1:1'))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame(0, $response['http_status']);
        $this->assertSame('error', $response['status']);
        $this->assertSame('REQUEST_FAILED', $response['code']);
    }

    public function test_a_slow_response_times_out()
    {
        $started = microtime(true);
        $response = (new SpPayPhp(base_url: self::$host . '/slow', timeout: 1))->validateAccount('token', 'MTN', '0241234567');

        $this->assertSame('REQUEST_FAILED', $response['code']);
        $this->assertLessThan(2.5, microtime(true) - $started);
    }
}
