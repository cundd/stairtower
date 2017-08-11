<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

class HttpRequestClient
{
    private $port;
    private $hostname;
    private $lastCurlCommand = '';

    /**
     * HttpRequestClient constructor
     *
     * @param string $hostname
     * @param int    $port
     */
    public function __construct(string $hostname = '127.0.0.1', int $port = 1338)
    {
        $this->port = $port;
        $this->hostname = $hostname;
    }


    /**
     * Performs a REST request
     *
     * @param string           $request
     * @param string           $method
     * @param array            $bodyData
     * @param string|null|bool $rawResult
     * @return mixed|string
     */
    public function performRestRequest(
        string $request,
        string $method = 'GET',
        array $bodyData = null,
        &$rawResult = null
    ) {
        $url = sprintf('http://%s:%d/%s', $this->hostname, $this->port, $request);

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

        if (is_callable('curl_init')) {
            return $this->performRestRequestCurl($url, $method, $headers, $content, $rawResult);
        }

        return $this->performRestRequestFopen($url, $method, $headers, $content, $rawResult);
    }

    /**
     * Performs a REST request CURL
     *
     * @param string           $uri
     * @param string           $method
     * @param array            $headers
     * @param string           $content
     * @param string|null|bool $rawResult
     * @return mixed
     */
    protected function performRestRequestCurl(
        string $uri,
        string $method = 'GET',
        array $headers = [],
        string $content = null,
        &$rawResult = null
    ) {
        $ch = curl_init($uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array_map(
                function ($key, $value) {
                    return "$key: $value";
                },
                array_keys($headers),
                $headers
            )
        );
        if (null !== $content) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }

        $rawResult = curl_exec($ch);
        if (false === $rawResult) {
            return $rawResult;
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($rawResult, $headerSize);

        curl_close($ch);

        return $this->decodeResponseBody($body);
    }

    /**
     * Performs a REST request using file_get_contents
     *
     * @param string           $uri
     * @param string           $method
     * @param array            $headers
     * @param string           $content
     * @param string|null|bool $rawResult
     * @return mixed
     */
    private function performRestRequestFopen(
        string $uri,
        string $method = 'GET',
        array $headers = [],
        string $content = null,
        &$rawResult = null
    ) {
        $options = [
            'http' => [
                'header' => implode("\r\n", $headers),
                'method' => $method,
            ],
        ];
        if (null !== $content) {
            $options['http']['content'] = $content;
        }

        $context = stream_context_create($options);
        $rawResult = @file_get_contents($uri, false, $context);

        return $this->decodeResponseBody($rawResult);
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
            $uri = sprintf('http://%s:%d/%s', $this->hostname, $this->port, $uri);
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
     * @param $body
     * @return mixed
     * @throws \Exception
     */
    private function decodeResponseBody($body)
    {
        $parsedResponseBody = json_decode($body, true);

        if (null === $parsedResponseBody) {
            throw new \Exception(
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

        return $parsedResponseBody;
    }
}
