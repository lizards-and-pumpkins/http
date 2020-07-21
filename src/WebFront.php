<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Http\Routing\HttpRouterChain;
use LizardsAndPumpkins\Core\Factory\Factory;
use LizardsAndPumpkins\Core\Factory\MasterFactory;

abstract class WebFront
{
    /**
     * @var MasterFactory
     */
    private $masterFactory;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var HttpRouterChain
     */
    private $routerChain;

    /**
     * @var Factory
     */
    private $implementationSpecificFactory;

    public function __construct(HttpRequest $request, Factory $implementationSpecificFactory)
    {
        $this->request = $request;
        $this->implementationSpecificFactory = $implementationSpecificFactory;
    }

    public function run(): HttpResponse
    {
        $response = $this->processRequest();
        $response->send();

        return $response;
    }

    public function processRequest(): HttpResponse
    {
        $this->buildFactory();
        $this->buildRouterChain();

        $requestHandler = $this->routerChain->route($this->request);

        return $requestHandler->process($this->request);
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    public function getImplementationSpecificFactory(): Factory
    {
        return $this->implementationSpecificFactory;
    }

    public function getMasterFactory(): MasterFactory
    {
        $this->buildFactory();

        return $this->masterFactory;
    }

    abstract protected function createMasterFactory(): MasterFactory;

    abstract protected function registerFactories(MasterFactory $factory): void;

    abstract protected function registerRouters(HttpRouterChain $routerChain): void;

    private function buildFactory(): void
    {
        if (null !== $this->masterFactory) {
            return;
        }

        $this->masterFactory = $this->createMasterFactory();
        $this->registerFactories($this->masterFactory);
    }

    private function buildRouterChain(): void
    {
        $this->routerChain = $this->masterFactory->createHttpRouterChain();
        $this->registerRouters($this->routerChain);
    }
}
