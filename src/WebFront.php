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

    abstract protected function createMasterFactory(): MasterFactory;

    abstract protected function registerFactories(MasterFactory $factory): void;

    abstract protected function registerRouters(HttpRouterChain $routerChain): void;

    final protected function getRequest(): HttpRequest
    {
        return $this->request;
    }

    final public function getImplementationSpecificFactory(): Factory
    {
        return $this->implementationSpecificFactory;
    }

    private function buildFactory(): void
    {
        if (null !== $this->masterFactory) {
            return;
        }

        $this->masterFactory = $this->createMasterFactory();
        $this->validateMasterFactory();
        $this->registerFactories($this->masterFactory);
    }

    private function buildRouterChain(): void
    {
        $this->routerChain = $this->masterFactory->createHttpRouterChain();
        $this->registerRouters($this->routerChain);
    }

    public function getMasterFactory(): MasterFactory
    {
        $this->buildFactory();

        return $this->masterFactory;
    }

    private function validateMasterFactory(): void
    {
        if (! ($this->masterFactory instanceof MasterFactory)) {
            throw new \InvalidArgumentException(sprintf(
                'Factory is not of type MasterFactory but "%s"',
                $this->getExceptionMessageClassNameRepresentation($this->masterFactory)
            ));
        }
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getExceptionMessageClassNameRepresentation($value): string
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_null($value)) {
            return 'NULL';
        }

        return (string) $value;
    }
}
