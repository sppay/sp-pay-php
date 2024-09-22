<?php

namespace Sppay\SpPayPhp;

class SpPayApiRequest
{
    protected $baseUrl;
    protected $method;
    protected $endppoint;
    protected $body;
    protected $bearerToken;

    public function __construct($baseUrl, $method = 'POST', $endppoint, $body = [], $bearerToken = null)
    {
        $this->baseUrl = $baseUrl;
        $this->method = $method;
        $this->endppoint = $endppoint;
        $this->body = $body;
        $this->bearerToken = $bearerToken;
    }

    public function sendRequest()
    {
        try {
            // Initialize cURL
            $ch = curl_init($this->baseUrl . $this->endppoint);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $this->bearerToken", // Set the authorization header
                "Content-Type: application/json", // Optional: Set the content type if needed
            ]);

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
                // Return the response
                return json_decode($response, true);
            }

            // Close the cURL session
            curl_close($ch);
        } catch (\Throwable $th) {
            return 'API Request Error: ' . $th->getMessage();
        }
    }
}
