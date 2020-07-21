<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http\Routing;

use LizardsAndPumpkins\Http\HttpRequest;
use LizardsAndPumpkins\Http\HttpResponse;

interface HttpRequestHandler
{
    public function canProcess(HttpRequest $request): bool;

    public function process(HttpRequest $request): HttpResponse;
}
