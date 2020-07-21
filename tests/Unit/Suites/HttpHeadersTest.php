<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Http\Exception\HeaderNotPresentException;
use LizardsAndPumpkins\Http\Exception\InvalidHttpHeadersException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Http\HttpHeaders
 */
class HttpHeadersTest extends TestCase
{
    public function testItThrowsAnExceptionIfAnInvalidHeaderIsRequested(): void
    {
        $this->expectException(HeaderNotPresentException::class);
        HttpHeaders::fromArray([])->get('a-http-request-header');
    }

    public function testItReturnsFalseIfTheRequestedHeaderIsNotPresent(): void
    {
        $this->assertFalse(HttpHeaders::fromArray([])->has('not-present-header'));
    }

    public function testItReturnsTrueIfTheRequestHeaderIsPresent(): void
    {
        $headerName = 'a-http-header';
        $this->assertTrue(HttpHeaders::fromArray([$headerName => 'the-header-value'])->has($headerName));
    }

    public function testItChecksForHeaderPresenceInACaseInsensitiveManner(): void
    {
        $this->assertTrue(HttpHeaders::fromArray(['A-http-header' => 'the-header-value'])->has('a-HTTP-header'));
    }

    public function testItReturnsTheHeaderIfPresent(): void
    {
        $headerName = 'a-http-header';
        $headerValue = 'the-header-value';
        $this->assertSame($headerValue, HttpHeaders::fromArray([$headerName => $headerValue])->get($headerName));
    }

    public function testItReturnsTheHeaderValueUsingTheHeaderNameInACaseInsensitiveManner(): void
    {
        $headerName = 'a-http-header';
        $headerValue = 'the-header-value';
        $headers = HttpHeaders::fromArray([$headerName => $headerValue]);
        $this->assertSame($headerValue, $headers->get(strtoupper($headerName)));
    }

    public function testEmptyArrayIsReturnedInCaseNoHeadersWereSet(): void
    {
        $headers = HttpHeaders::fromArray([]);
        $this->assertEquals([], $headers->getAll());
    }

    public function testAllHeadersAreReturned(): void
    {
        $headersArray = ['Header-1-Name' => 'header 1 value', 'Header-2-Name' => 'header 2 value'];
        $headers = HttpHeaders::fromArray($headersArray);

        $this->assertEquals($headersArray, $headers->getAll());
    }

    /**
     * @dataProvider getMalformedHeadersSources
     * @param mixed[] $malformedHeadersSource
     */
    public function testExceptionIsThrownDuringAttemptToCreateHeadersFromArrayContainingNonStringKeysOrValues(
        array $malformedHeadersSource
    ): void {
        $this->expectException(InvalidHttpHeadersException::class);
        HttpHeaders::fromArray($malformedHeadersSource);
    }

    /**
     * @return array[]
     */
    public function getMalformedHeadersSources(): array
    {
        return [
            [['foo' => 1]],
            [['bar']],
            [[1 => []]],
        ];
    }

    public function testHeadersCanBeCreatedFromGlobals(): void
    {
        $this->assertInstanceOf(HttpHeaders::class, HttpHeaders::fromGlobalRequestHeaders());
    }

    public function testHeadersContainGlobalValues(): void
    {
        $dummyValue = 'bar';
        $_SERVER['HTTP_FOO'] = $dummyValue;

        $result = HttpHeaders::fromGlobalRequestHeaders();

        unset($_SERVER['HTTP_FOO']);

        $this->assertSame(['Foo' => $dummyValue], $result->getAll());
    }

    public function testOnlyHttpGlobalsAreUsedForCreatingHeaders(): void
    {
        $_SERVER['FOO_BAR'] = 'baz';

        $result = HttpHeaders::fromGlobalRequestHeaders();

        unset($_SERVER['FOO_BAR']);

        $this->assertSame([], $result->getAll());
    }

    public function testHeadersCreatedFromGlobalsAreNormalized(): void
    {
        $_SERVER['HTTP_FOO_BAR'] = 'bar';

        $result = HttpHeaders::fromGlobalRequestHeaders();

        unset($_SERVER['HTTP_FOO_BAR']);

        $this->assertTrue($result->has('Foo-Bar'));
    }
}
