<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

/**
 * @covers \LizardsAndPumpkins\Http\HttpPutRequest
 * @covers \LizardsAndPumpkins\Http\HttpRequest
 * @uses   \LizardsAndPumpkins\Http\HttpUrl
 * @uses   \LizardsAndPumpkins\Http\HttpGetRequest
 * @uses   \LizardsAndPumpkins\Http\HttpHeaders
 * @uses   \LizardsAndPumpkins\Http\HttpPostRequest
 * @uses   \LizardsAndPumpkins\Http\HttpRequestBody
 */
class HttpPutRequestTest extends AbstractHttpRequestTest
{
    /**
     * @var HttpPutRequest
     */
    private $request;

    final protected function setUp(): void
    {
        /** @var HttpUrl $stubHttpUrl */
        $stubHttpUrl = $this->createMock(HttpUrl::class);

        $this->request = HttpRequest::fromParameters(
            HttpRequest::METHOD_PUT,
            $stubHttpUrl,
            HttpHeaders::fromArray([]),
            new HttpRequestBody('')
        );
    }

    public function testPutRequestIsReturned(): void
    {
        $this->assertInstanceOf(HttpPutRequest::class, $this->request);
    }

    public function testPutMethodNameIsReturned(): void
    {
        $result = $this->request->getMethod();
        $this->assertSame(HttpRequest::METHOD_PUT, $result);
    }
}
