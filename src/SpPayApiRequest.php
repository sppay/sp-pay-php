<?php

namespace Sppay\SpPayPhp;

class SpPayApiRequest
{
    protected $baseUrl;
    protected $method;
    protected $endpoint;
    protected $body;
    protected $bearerToken;

    public function __construct($baseUrl, $endpoint, $method = 'POST', $body = [], $bearerToken = null)
    {
        $this->baseUrl = $baseUrl;
        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->body = $body;
        $this->bearerToken = $bearerToken;
    }

    public function sendRequest()
    {
        try {
            // Initialize cURL
            $ch = curl_init($this->baseUrl . $this->endpoint);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string

            // Initialize headers array
            $headers = [];

            // Optionally set the Authorization header if the bearer token is provided
            if ($this->bearerToken) {
                $headers[] = "Authorization: Bearer $this->bearerToken"; // Set the authorization header
            }

            // Set Content-Type header.
            $headers[] = "Content-Type: application/json";

            // Set headers for the cURL request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Set the HTTP method
            if (strtoupper($this->method) === 'POST') {
                curl_setopt($ch, CURLOPT_POST, true); // Set method to POST
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body)); // Set the request body
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method)); // Set custom method (GET, DELETE, etc.)
            }

            // Execute the request
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                return 'API Request Error: ' . curl_error($ch);
            } else {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $decoded = json_decode($response, true);

                // Anything in front of the API, such as a proxy or a firewall
                // challenge, can answer with HTML. Say so rather than failing
                // to unpack it.
                if (! is_array($decoded)) {
                    return [
                        'code' => $code,
                        'error' => 'invalid_response',
                        'message' => 'The API returned a response that is not JSON',
                        'content_type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
                        'body' => substr((string) $response, 0, 500),
                    ];
                }

                // Return the response
                return [
                    'code' => $code,
                    ...$decoded,
                ];
            }

            // Close the cURL session
            curl_close($ch);
        } catch (\Throwable $th) {
            return 'API Request Error: ' . $th->getMessage();
        }
    }
}
