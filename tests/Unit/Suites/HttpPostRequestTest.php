<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

/**
 * @covers \LizardsAndPumpkins\Http\HttpPostRequest
 * @covers \LizardsAndPumpkins\Http\HttpRequest
 * @uses   \LizardsAndPumpkins\Http\HttpGetRequest
 * @uses   \LizardsAndPumpkins\Http\HttpUrl
 * @uses   \LizardsAndPumpkins\Http\HttpHeaders
 * @uses   \LizardsAndPumpkins\Http\HttpRequestBody
 */
class HttpPostRequestTest extends AbstractHttpRequestTest
{
    /**
     * @var HttpPostRequest
     */
    private $request;

    final protected function setUp(): void
    {
        /** @var HttpUrl $stubHttpUrl */
        $stubHttpUrl = $this->createMock(HttpUrl::class);

        $this->request = HttpRequest::fromParameters(
            HttpRequest::METHOD_POST,
            $stubHttpUrl,
            HttpHeaders::fromArray([]),
            new HttpRequestBody('')
        );
    }

    public function testPostRequestIsReturned(): void
    {
        $this->assertInstanceOf(HttpPostRequest::class, $this->request);
    }

    public function testPostMethodNameIsReturned(): void
    {
        $result = $this->request->getMethod();
        $this->assertSame(HttpRequest::METHOD_POST, $result);
    }
}
