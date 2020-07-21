<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http\Routing;

use LizardsAndPumpkins\Http\HttpRequest;

class UnknownHttpRequestMethodRouter implements HttpRouter
{
    /**
     * @var HttpRequestHandler
     */
    private $requestHandler;

    public function __construct(HttpRequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    public function route(HttpRequest $request): ?HttpRequestHandler
    {
        if (! $this->requestHandler->canProcess($request)) {
            return null;
        }

        return $this->requestHandler;
    }
}
