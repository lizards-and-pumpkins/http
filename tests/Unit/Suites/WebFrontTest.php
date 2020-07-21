<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

use LizardsAndPumpkins\Core\Factory\Factory;
use LizardsAndPumpkins\Http\Routing\HttpRequestHandler;
use LizardsAndPumpkins\Http\Routing\HttpRouterChain;
use LizardsAndPumpkins\Core\Factory\MasterFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\Http\WebFront
 */
class WebFrontTest extends TestCase
{
    /**
     * @var HttpRequest
     */
    private $dummyHttpRequest;

    /**
     * @var Factory
     */
    private $dummyFactory;

    /**
     * @var HttpResponse
     */
    private $mockHttpResponse;

    /**
     * @var WebFront
     */
    private $webFront;

    final protected function setUp(): void
    {
        $this->dummyHttpRequest = $this->createMock(HttpRequest::class);
        $this->dummyFactory = $this->createMock(Factory::class);

        $this->mockHttpResponse = $this->createMock(HttpResponse::class);

        $mockHttpRequestHandler = $this->createMock(HttpRequestHandler::class);
        $mockHttpRequestHandler->method('process')->willReturn($this->mockHttpResponse);

        $stubRouterChain = $this->createMock(HttpRouterChain::class);
        $stubRouterChain->method('route')->willReturn($mockHttpRequestHandler);

        $stubMasterFactory = $this->getMockBuilder(MasterFactory::class)
            ->onlyMethods(get_class_methods(MasterFactory::class))
            ->addMethods(['createHttpRouterChain'])
            ->getMock();
        $stubMasterFactory->method('createHttpRouterChain')->willReturn($stubRouterChain);

        $this->webFront = new class(
            $this->dummyHttpRequest,
            $stubMasterFactory,
            $this->dummyFactory
        ) extends WebFront {
            /**
             * @var MasterFactory
             */
            private $testMasterFactory;

            /**
             * @var int
             */
            private $masterFactoryCreationCount = 0;

            public function __construct(
                HttpRequest $request,
                MasterFactory $testMasterFactory,
                Factory $stubFactory
            ) {
                parent::__construct($request, $stubFactory);

                $this->testMasterFactory = $testMasterFactory;
            }

            public function getMasterFactoryCreationCount(): int
            {
                return $this->masterFactoryCreationCount;
            }

            final protected function createMasterFactory() : MasterFactory
            {
                $this->masterFactoryCreationCount++;

                return $this->testMasterFactory;
            }

            final protected function registerFactories(MasterFactory $factory): void
            {
            }

            final protected function registerRouters(HttpRouterChain $routerChain): void
            {
            }
        };
    }

    public function testReturnsResponse(): void
    {
        $this->assertInstanceOf(HttpResponse::class, $this->webFront->run());
    }

    public function testSendsResponse(): void
    {
        $this->mockHttpResponse->expects($this->once())->method('send');
        $this->webFront->run();
    }

    public function testReturnsRequest(): void
    {
        $this->assertSame($this->dummyHttpRequest, $this->webFront->getRequest());
    }

    public function testReturnsImplementationSpecificFactory(): void
    {
        $this->assertSame($this->dummyFactory, $this->webFront->getImplementationSpecificFactory());
    }

    public function testReturnsMasterFactory(): void
    {
        $this->assertInstanceOf(MasterFactory::class, $this->webFront->getMasterFactory());
    }

    public function testReturnsSameInstanceOfMasterFactoryOnSubsequentCalls(): void
    {
        $this->assertSame(0, $this->webFront->getMasterFactoryCreationCount());

        $this->webFront->getMasterFactory();
        $this->webFront->getMasterFactory();

        $this->assertSame(1, $this->webFront->getMasterFactoryCreationCount());
    }
}
