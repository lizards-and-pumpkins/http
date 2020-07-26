<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

/**
 * @covers \LizardsAndPumpkins\Http\HttpGetRequest
 * @covers \LizardsAndPumpkins\Http\HttpRequest
 * @uses   \LizardsAndPumpkins\Http\HttpUrl
 * @uses   \LizardsAndPumpkins\Http\HttpHeaders
 * @uses   \LizardsAndPumpkins\Http\HttpRequestBody
 */
class HttpGetRequestTest extends AbstractHttpRequestTest
{
    /**
     * @var HttpGetRequest
     */
    private $request;

    final protected function setUp(): void
    {
        /** @var HttpUrl $stubHttpUrl */
        $stubHttpUrl = $this->createMock(HttpUrl::class);

        $this->request = HttpRequest::fromParameters(
            HttpRequest::METHOD_GET,
            $stubHttpUrl,
            HttpHeaders::fromArray([]),
            new HttpRequestBody('')
        );
    }

    public function testGetRequestIsReturned(): void
    {
        $this->assertInstanceOf(HttpGetRequest::class, $this->request);
    }

    public function testGetRequestIsReturnedForHeadRequests(): void
    {
        /** @var HttpUrl $stubHttpUrl */
        $stubHttpUrl = $this->createMock(HttpUrl::class);

        $request = HttpRequest::fromParameters(
            HttpRequest::METHOD_HEAD,
            $stubHttpUrl,
            HttpHeaders::fromArray([]),
            new HttpRequestBody('')
        );

        $this->assertInstanceOf(HttpGetRequest::class, $request);
    }

    public function testGetMethodNameIsReturned(): void
    {
        $result = $this->request->getMethod();
        $this->assertSame(HttpRequest::METHOD_GET, $result);
    }
}
