<?php

namespace App\Services;

use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\Http\GraphRawResponse;
use Facebook\Exceptions\FacebookSDKException;

class CustomFacebookCurlClient extends FacebookCurlHttpClient
{
    /**
     * Override to disable SSL verification for local development
     */
    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $options = [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $this->compileRequestHeaders($headers),
            CURLOPT_URL => $url,
            CURLOPT_CONNECTTIMEOUT => 30, // Increased from 10 to 30 seconds
            CURLOPT_TIMEOUT => max($timeOut, 60), // Minimum 60 seconds
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYHOST => 0, // Disable SSL verification for local dev
            CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification for local dev
            CURLOPT_DNS_CACHE_TIMEOUT => 120, // Cache DNS for 2 minutes
            // CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Allow both IPv4 and IPv6
            CURLOPT_FOLLOWLOCATION => true, // Follow redirects
            CURLOPT_MAXREDIRS => 5, // Max 5 redirects
            // Use Google DNS servers as fallback
            CURLOPT_DNS_SERVERS => '8.8.8.8,8.8.4.4,1.1.1.1',
        ];

        if ($method !== 'GET') {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $rawResponse = curl_exec($ch);

        if ($rawResponse === false) {
            $error = curl_error($ch);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            throw new FacebookSDKException($error, $errorCode);
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Extract headers and body
        $rawHeaders = $headerSize > 0 ? mb_substr($rawResponse, 0, $headerSize) : '';
        $rawBody = mb_substr($rawResponse, $headerSize);

        // Facebook SDK expects HTTP/1.1 or HTTP/1.0 format
        // But cURL might return HTTP/2, so we need to convert it
        $rawHeaders = preg_replace('|^HTTP/2\s+|', 'HTTP/1.1 ', $rawHeaders);

        // Ensure we have a valid HTTP status line in headers
        if (empty($rawHeaders) || !preg_match('|^HTTP/\d\.\d\s+\d+|', trim($rawHeaders))) {
            // Build proper headers with HTTP status line
            $statusText = $this->getHttpStatusText($httpCode);
            $rawHeaders = "HTTP/1.1 {$httpCode} {$statusText}\r\n" . $rawHeaders;
        }

        return new GraphRawResponse($rawHeaders, $rawBody);
    }

    /**
     * Compile headers - use parent method if available
     */
    public function compileRequestHeaders(array $headers)
    {
        $compiled = [];
        foreach ($headers as $key => $value) {
            $compiled[] = $key . ': ' . $value;
        }
        return $compiled;
    }

    /**
     * Get HTTP status text for a given status code
     */
    protected function getHttpStatusText($code)
    {
        $statusTexts = [
            200 => 'OK',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];

        return $statusTexts[$code] ?? 'Unknown';
    }
}
