<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Http\Routing\HttpRouterChain;
use LizardsAndPumpkins\Http\Routing\ResourceNotFoundRouter;

class HttpFactory
{
    public function createResourceNotFoundRouter(): ResourceNotFoundRouter
    {
        return new ResourceNotFoundRouter();
    }

    public function createHttpRouterChain(): HttpRouterChain
    {
        return new HttpRouterChain();
    }
}
