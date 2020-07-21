<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http\Routing;

use LizardsAndPumpkins\Http\HttpRequest;

interface HttpRouter
{
    public function route(HttpRequest $request): ?HttpRequestHandler;
}
