<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Core\Factory\Factory;
use LizardsAndPumpkins\Http\Routing\HttpRouterChain;
use LizardsAndPumpkins\Http\Routing\ResourceNotFoundRouter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Http\HttpFactory
 */
class HttpFactoryTest extends TestCase
{
    public function testIsFactory(): void
    {
        $this->assertInstanceOf(Factory::class, new HttpFactory);
    }

    public function testReturnsHttpRouterChain(): void
    {
        $this->assertInstanceOf(HttpRouterChain::class, (new HttpFactory)->createHttpRouterChain());
    }

    public function testReturnsResourceNotFoundRouter(): void
    {
        $this->assertInstanceOf(ResourceNotFoundRouter::class, (new HttpFactory)->createResourceNotFoundRouter());
    }
}
