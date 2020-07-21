<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Http\Exception\InvalidUrlStringException;
use LizardsAndPumpkins\Http\Exception\QueryParameterDoesNotExistException;
use LizardsAndPumpkins\Http\Exception\UnknownProtocolException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Http\HttpUrl
 */
class HttpUrlTest extends TestCase
{
    /**
     * @dataProvider urlStringProvider
     */
    public function testReturnsUrlString(string $urlString)
    {
        $this->assertSame($urlString, (string) HttpUrl::fromString($urlString));
    }

    /**
     * @return array[]
     */
    public function urlStringProvider(): array
    {
        return [
            ['http://example.com'],
            ['https://example.com'],
            ['//example.com'],
            ['http://example.com/'],
            ['http://example.com/path'],
            ['http://example.com/path/path/path/'],
            ['http://example.com?foo=bar'],
            ['http://example.com/?foo=bar'],
            ['http://example.com/path/?foo=bar'],
            ['http://example.com:123'],
            ['https://example.com:123'],
            ['//example.com:123'],
            ['http://example.com:123/'],
            ['http://example.com:123/path'],
            ['http://example.com:321/path/path/path/'],
            ['http://example.com:321?foo=bar'],
            ['http://example.com:321/?foo=bar'],
            ['http://example.com:321/path/?foo=bar'],
        ];
    }

    public function testThrowsAnExceptionForNonHttpUrls()
    {
        $this->expectException(UnknownProtocolException::class);
        HttpUrl::fromString('ftp://user:pass@example.com');
    }

    public function testThrowsAnExceptionDuringAttemptToCreateUrlFromInvalidString()
    {
        $this->expectException(InvalidUrlStringException::class);
        HttpUrl::fromString('this is not a valid url');
    }

    public function testReturnsFalseIfQueryParameterIsNotSet()
    {
        $url = HttpUrl::fromString('http://example.com');
        $this->assertFalse($url->hasQueryParameter('foo'));
    }

    public function testReturnsTrueIfQueryParameterIsSet()
    {
        $url = HttpUrl::fromString('http://example.com?foo=bar');
        $this->assertTrue($url->hasQueryParameter('foo'));
    }

    public function testThrowsAnExceptionDuringAttemptToRetrieveNonExistingQueryParameterValue()
    {
        $this->expectException(QueryParameterDoesNotExistException::class);
        HttpUrl::fromString('http://example.com/path')->getQueryParameter('foo');
    }

    public function testQueryParameterIsReturned()
    {
        $url = HttpUrl::fromString('http://example.com/?foo=bar&baz=qux');
        $this->assertEquals('bar', $url->getQueryParameter('foo'));
    }

    public function testReturnsTrueIfThereAreQueryParameters()
    {
        $url = HttpUrl::fromString('http://example.com/?foo=bar&baz=qux');
        $this->assertTrue($url->hasQueryParameters());
    }

    public function testReturnsFalseIfThereAreQueryParameters()
    {
        $url = HttpUrl::fromString('http://example.com/foo/');
        $this->assertFalse($url->hasQueryParameters());
    }

    /**
     * @dataProvider requestHostDataProvider
     */
    public function testReturnsHost(string $host, string $expected)
    {
        $url = HttpUrl::fromString('http://' . $host . '/path/to/some-page');
        $this->assertSame($expected, $url->getHost());
    }

    /**
     * @return array[]
     */
    public function requestHostDataProvider(): array
    {
        return [
            'top'      => ['example.com', 'example.com'],
            'sub'      => ['www.example.com', 'www.example.com'],
            'special'  => ['über.com', 'über.com'],
            'punycode' => ['xn--ber-goa.com', 'über.com'],
            'ip4'      => ['127.0.0.1', '127.0.0.1'],
        ];
    }
}
