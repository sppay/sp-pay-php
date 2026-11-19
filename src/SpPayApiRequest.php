<?php

namespace Sppay\SpPayPhp;

use JsonException;

class SpPayApiRequest
{
    public function __construct(
        private string $baseUrl,
        private string $endpoint,
        private string $method = 'POST',
        private array $body = [],
        private ?string $bearerToken = null,
        private int $timeout = 60,
    ) {
    }

    public function sendRequest(): array
    {
        try {
            $payload = json_encode($this->body, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return ['code' => 0, 'error' => 'invalid_request', 'message' => $e->getMessage()];
        }

        $headers = ['Content-Type: application/json'];
        if ($this->bearerToken) {
            $headers[] = "Authorization: Bearer $this->bearerToken";
        }

        $ch = curl_init($this->baseUrl . $this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => $this->timeout,
        ]);

        if (strtoupper($this->method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
        }

        $response = curl_exec($ch);

        if ($response === false) {
            return ['code' => 0, 'error' => 'request_failed', 'message' => curl_error($ch)];
        }

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $decoded = json_decode($response, true);

        if (! is_array($decoded)) {
            return [
                'code' => $code,
                'error' => 'invalid_response',
                'message' => 'The API returned a response that is not JSON',
                'content_type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
                'body' => substr($response, 0, 500),
            ];
        }

        if (array_key_exists('code', $decoded)) {
            $decoded['error_code'] = $decoded['code'];
        }

        return [...$decoded, 'code' => $code];
    }
}
