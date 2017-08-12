<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests;

class HttpRequestClient
{
    private $uri;
    private $lastCurlCommand = '';

    /**
     * HttpRequestClient constructor
     *
     * @param string $uri
     */
    public function __construct(string $uri = '127.0.0.1:1338')
    {
        $this->uri = $uri;
    }

    /**
     * Performs a REST request
     *
     * @param string           $request
     * @param string           $method
     * @param array            $bodyData
     * @param string|null|bool $rawResult
     * @return HttpResponse
     */
    public function performRestRequest(
        string $request,
        string $method = 'GET',
        array $bodyData = null,
        &$rawResult = null
    ): HttpResponse {
        $url = sprintf('http://%s/%s', $this->uri, $request);

        if ($bodyData) {
            $content = http_build_query($bodyData);
            $contentType = 'application/x-www-form-urlencoded';

            $headers = [
                'Content-Type'   => $contentType,
                'Content-Length' => strlen($content),
            ];
        } else {
            $content = null;
            $headers = [
                'Content-Type' => 'application/json',
            ];
        }

        $this->lastCurlCommand = $this->buildCurlCommand($url, $method, $bodyData, $headers);

        if (!is_callable('curl_init')) {
            fwrite(
                STDERR,
                'Curl is not available. Falling back to file_get_contents() which does not support all operations'
            );

            return $this->performRestRequestFopen($url, $method, $headers, $content, $rawResult);
        }

        return $this->performRestRequestCurl($url, $method, $headers, $content, $rawResult);
    }

    /**
     * Performs a REST request CURL
     *
     * @param string           $uri
     * @param string           $method
     * @param array            $headers
     * @param string           $content
     * @param string|null|bool $rawResult
     * @return HttpResponse
     */
    protected function performRestRequestCurl(
        string $uri,
        string $method = 'GET',
        array $headers = [],
        string $content = null,
        &$rawResult = null
    ): HttpResponse {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->flattenHeaders($headers));
        if (null !== $content) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }

        $rawResult = curl_exec($ch);


        if (false === $rawResult) {
            return new HttpResponse(0, null, $rawResult, [], new \Exception(curl_error($ch), curl_errno($ch)));
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $body = substr($rawResult, $headerSize);
        $rawHeader = substr($rawResult, 0, $headerSize);

        curl_close($ch);

        return $this->decodeResponseBody($status, explode("\n", $rawHeader), $body);
    }

    /**
     * Performs a REST request using file_get_contents
     *
     * @param string           $uri
     * @param string           $method
     * @param array            $headers
     * @param string           $content
     * @param string|null|bool $rawResult
     * @return HttpResponse
     */
    private function performRestRequestFopen(
        string $uri,
        string $method = 'GET',
        array $headers = [],
        string $content = null,
        &$rawResult = null
    ): HttpResponse {
        $options = [
            'http' => [
                'header' => implode("\r\n", $this->flattenHeaders($headers)),
                'method' => $method,
            ],
        ];
        if (null !== $content) {
            $options['http']['content'] = $content;
        }

        $context = stream_context_create($options);
        $rawResult = file_get_contents($uri, false, $context);

        $headers = array_merge([], $http_response_header);
        $statusLine = array_shift($headers);
        list($statusCode,) = sscanf($statusLine, 'HTTP/1.1 %d %s');

        return $this->decodeResponseBody((int)$statusCode, $headers, $rawResult);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param null   $body
     * @param array  $headers
     * @param null   $basicAuth
     */
    public function debugCurlCommand(
        string $uri,
        string $method = 'GET',
        $body = null,
        array $headers = [],
        $basicAuth = null
    ) {
        fwrite(STDOUT, PHP_EOL . $this->buildCurlCommand($uri, $method, $body, $headers, $basicAuth) . PHP_EOL);
    }

    /**
     * @param string            $uri
     * @param string            $method
     * @param null|string|mixed $body      Will be ignored if NULL, otherwise will be JSON encoded if it is not a string
     * @param string[]          $headers   A dictionary of headers
     * @param string            $basicAuth String in the format "user:password"
     * @return string
     */
    public function buildCurlCommand(
        string $uri,
        string $method = 'GET',
        $body = null,
        array $headers = [],
        $basicAuth = null
    ) {
        if (substr($uri, 0, 7) !== 'http://' && substr($uri, 0, 8) !== 'https://') {
            $uri = sprintf('http://%s/%s', $this->uri, $uri);
        }

        $command = ['curl'];

        // Method
        $command[] = '-X';
        $command[] = escapeshellarg($method);

        // Basic auth
        if (null !== $basicAuth) {
            $command[] = '-u';
            $command[] = escapeshellarg($basicAuth);
        }

        // Body
        if (null !== $body) {
            if (!is_string($body)) {
                $body = http_build_query($body);
            }
            if (!isset($headers['Content-Type'])) {
                $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            }
            if (!isset($headers['Content-Length'])) {
                $headers['Content-Length'] = strlen($body);
            }
            $command[] = '-d';
            $command[] = escapeshellarg($body);
        }

        // Headers
        foreach ($headers as $key => $value) {
            if (!is_int($key)) {
                $command[] = '--header ' . escapeshellarg("$key: $value");
            } else {
                $command[] = '--header ' . escapeshellarg("$value");
            }
        }

        // URL
        $command[] = escapeshellarg($uri);

        return implode(' ', $command);
    }

    /**
     * @return string
     */
    public function getLastCurlCommand()
    {
        return $this->lastCurlCommand;
    }

    /**
     * @param int         $status
     * @param string[]    $headers
     * @param string|null $body
     * @return HttpResponse
     */
    private function decodeResponseBody(int $status, array $headers, $body): HttpResponse
    {
        $error = null;
        $parsedResponseBody = [];

        if (is_string($body)) {
            $parsedResponseBody = json_decode($body, true);

            if (null === $parsedResponseBody) {
                $error = new \Exception(
                    sprintf(
                        'JSON decode failed with message %s for body "%s"%s%s',
                        json_last_error_msg(),
                        $body,
                        PHP_EOL,
                        $this->getLastCurlCommand()
                    ),
                    json_last_error()
                );
            }
        }


        return new HttpResponse(
            $status,
            $parsedResponseBody,
            $body,
            $headers,
            $error
        );
    }

    /**
     * @param array $headers
     * @return array
     */
    private function flattenHeaders(array $headers): array
    {
        return array_map(
            function ($key, $value) {
                return "$key: $value";
            },
            array_keys($headers),
            $headers
        );
    }
}
