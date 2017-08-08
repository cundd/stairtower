<?php


namespace Cundd\Stairtower\Tests\Unit;


class HttpRequestClient
{
    private $port;
    private $hostname;

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
     * @param string $request
     * @param string $method
     * @param array  $arguments
     * @param mixed  $jsonContent
     *
     * @return mixed|string
     */
    public function performRestRequest(string $request, string $method = 'GET', array $arguments = [], $jsonContent = null)
    {
        $url = sprintf('http://%s:%d/%s', $this->hostname, $this->port, $request);

        if ($jsonContent) {
            $content = json_encode($jsonContent);
            $contentType = 'application/json';
        } else {
            $content = http_build_query($arguments);
            $contentType = 'application/x-www-form-urlencoded';
        }

        $headers = [
            'Content-Type: ' . $contentType,
            'Content-Length: ' . strlen($content),
        ];

        //printf('Request %s %d %s' . PHP_EOL, $method, strlen($content), $url);


        if (is_callable('curl_init')) {
            return $this->performRestRequestCurl($url, $method, $headers, $content);
        }

        return $this->performRestRequestFopen($url, $method, $headers, $content);
    }

    /**
     * Performs a REST request CURL
     *
     * @param string $request
     * @param string $method
     * @param array  $headers
     * @param string $content
     *
     * @return mixed
     */
    protected function performRestRequestCurl($request, $method = 'GET', $headers = [], $content = '')
    {
        $ch = curl_init($request);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);


        $response = curl_exec($ch);
        if (false === $response) {
            return $response;
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        curl_close($ch);

        if ($body) {
            return json_decode($body, true);
        }

        echo 'Header: ' . PHP_EOL . $header . PHP_EOL;


        return $response;
    }

    /**
     * Performs a REST request using file_get_contents
     *
     * @param string $request
     * @param string $method
     * @param array  $headers
     * @param string $content
     *
     * @return mixed
     */
    private function performRestRequestFopen($request, $method = 'GET', $headers = [], $content = '')
    {
        $options = [
            'http' => [
                'header'  => implode("\r\n", $headers),
                'method'  => $method,
                'content' => $content,
            ],
        ];
        $context = stream_context_create($options);
        $response = @file_get_contents($request, false, $context);
        if ($response) {
            return json_decode($response, true);
        }

        return $response;
    }
}