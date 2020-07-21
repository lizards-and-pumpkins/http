<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http\Routing;

use LizardsAndPumpkins\Http\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Http\Routing\ResourceNotFoundRouter
 */
class ResourceNotFoundRouterTest extends TestCase
{
    public function testInstanceOfResourceNotFoundRequestHandlerIsReturned()
    {
        /** @var HttpRequest|\PHPUnit_Framework_MockObject_MockObject $stubRequest */
        $stubRequest = $this->createMock(HttpRequest::class);
        $result = (new ResourceNotFoundRouter())->route($stubRequest);

        $this->assertInstanceOf(HttpRequestHandler::class, $result);
    }
}
