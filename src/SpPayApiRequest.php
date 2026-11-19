<?php

namespace Sppay\SpPayPhp;

class SpPayApiRequest
{
    public function __construct(
        private string $base_url,
        private string $endpoint,
        private string $method = 'POST',
        private array $body = [],
        private ?string $bearer_token = null,
    ) {
    }

    public function sendRequest(): array
    {
        $headers = ['Content-Type: application/json'];
        if ($this->bearer_token) {
            $headers[] = "Authorization: Bearer $this->bearer_token";
        }

        $ch = curl_init($this->base_url . $this->endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if (strtoupper($this->method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body));
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
        }

        $response = curl_exec($ch);

        if ($response === false) {
            return self::error(0, 'REQUEST_FAILED', curl_error($ch));
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $decoded = json_decode($response, true);

        if (! is_array($decoded)) {
            return self::error($http_status, 'INVALID_RESPONSE', 'The API returned a response that is not JSON') + [
                'content_type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
                'body' => substr($response, 0, 500),
            ];
        }

        return ['http_status' => $http_status, ...$decoded];
    }

    private static function error(int $http_status, string $code, string $message): array
    {
        return [
            'http_status' => $http_status,
            'status' => 'error',
            'message' => $message,
            'code' => $code,
        ];
    }
}
