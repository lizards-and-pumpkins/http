<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Http\Exception\CookieNotSetException;
use LizardsAndPumpkins\Http\Exception\QueryParameterDoesNotExistException;

abstract class HttpRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';

    /**
     * @var HttpUrl
     */
    private $url;

    /**
     * @var HttpHeaders
     */
    private $headers;

    /**
     * @var HttpRequestBody
     */
    private $body;

    private function __construct(HttpUrl $url, HttpHeaders $headers, HttpRequestBody $body)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function fromGlobalState(string $requestBody = ''): HttpRequest
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
            $protocol = 'https';
        }

        $url = HttpUrl::fromString($protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $headers = HttpHeaders::fromGlobalRequestHeaders();
        $body = new HttpRequestBody($requestBody);

        return self::fromParameters($requestMethod, $url, $headers, $body);
    }

    public static function fromParameters(
        string $requestMethod,
        HttpUrl $url,
        HttpHeaders $headers,
        HttpRequestBody $body
    ): HttpRequest {
        switch (strtoupper($requestMethod)) {
            case self::METHOD_GET:
            case self::METHOD_HEAD:
                return new HttpGetRequest($url, $headers, $body);
            case self::METHOD_POST:
                return new HttpPostRequest($url, $headers, $body);
            case self::METHOD_PUT:
                return new HttpPutRequest($url, $headers, $body);
            case self::METHOD_DELETE:
                return new HttpDeleteRequest($url, $headers, $body);
            default:
                $unknownMethodRequest = new HttpUnknownMethodRequest($url, $headers, $body);
                $unknownMethodRequest->setMethodCode($requestMethod);
                return $unknownMethodRequest;
                
        }
    }

    public function getUrl(): HttpUrl
    {
        return $this->url;
    }

    public function hasHeader(string $headerName): bool
    {
        return $this->headers->has($headerName);
    }

    public function getHeader(string $headerName): string
    {
        return $this->headers->get($headerName);
    }

    public function getRawBody(): string
    {
        return $this->body->toString();
    }

    abstract public function getMethod(): string;

    public function hasQueryParameter(string $parameterName)
    {
        return $this->url->hasQueryParameter($parameterName);
    }

    public function getQueryParameter(string $parameterName): ?string
    {
        if (! $this->hasQueryParameter($parameterName)) {
            throw new QueryParameterDoesNotExistException(
                sprintf('Query parameter "%s" does not exist', $parameterName)
            );
        }

        return $this->url->getQueryParameter($parameterName);
    }

    public function hasQueryParameters(): bool
    {
        return $this->url->hasQueryParameters();
    }

    /**
     * @return string[]
     */
    public function getCookies(): array
    {
        return $_COOKIE;
    }

    public function hasCookie(string $cookieName): bool
    {
        return isset($_COOKIE[$cookieName]);
    }

    public function getCookieValue(string $cookieName): string
    {
        if (! $this->hasCookie($cookieName)) {
            throw new CookieNotSetException(sprintf('Cookie with "%s" name is not set.', $cookieName));
        }

        return $_COOKIE[$cookieName];
    }

    public function getHost(): string
    {
        return $this->url->getHost();
    }
}
