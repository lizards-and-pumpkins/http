<?php

declare(strict_types=1);

namespace LizardsAndPumpkins;

use LizardsAndPumpkins\Core\Factory\Factory;
use LizardsAndPumpkins\Core\Factory\FactoryTrait;
use LizardsAndPumpkins\Core\Factory\MasterFactory;
use LizardsAndPumpkins\Core\Factory\MasterFactoryTrait;
use LizardsAndPumpkins\Http\HttpFactory;
use LizardsAndPumpkins\Http\HttpResponse;
use LizardsAndPumpkins\Http\HttpHeaders;
use LizardsAndPumpkins\Http\HttpRequestBody;
use LizardsAndPumpkins\Http\Routing\HttpRequestHandler;
use LizardsAndPumpkins\Http\Routing\HttpResourceNotFoundResponse;
use LizardsAndPumpkins\Http\HttpUrl;
use LizardsAndPumpkins\Http\HttpRequest;
use LizardsAndPumpkins\Http\Routing\HttpRouter;
use LizardsAndPumpkins\Http\Routing\HttpRouterChain;
use LizardsAndPumpkins\Http\WebFront;
use PHPUnit\Framework\TestCase;

class EdgeToEdgeImportCatalogTest extends TestCase
{
    private function getIntegrationTestsWebFront(
        HttpRequest $request,
        Factory $implementationSpecificFactory
    ): WebFront {
        return new class ($request, $implementationSpecificFactory) extends WebFront {
            final protected function createMasterFactory(): MasterFactory
            {
                return new class implements MasterFactory {
                    use MasterFactoryTrait;
                };
            }

            final protected function registerFactories(MasterFactory $masterFactory): void
            {
                $masterFactory->register(new HttpFactory());
            }

            final protected function registerRouters(HttpRouterChain $routerChain): void
            {
                $routerChain->register(new class implements HttpRouter {
                    public function route(HttpRequest $request): ?HttpRequestHandler
                    {
                        return new class implements HttpRequestHandler {
                            public function canProcess(HttpRequest $request): bool
                            {
                                return true;
                            }

                            public function process(HttpRequest $request): HttpResponse
                            {
                                if ($request->getUrl() !== 'http://example.com/') {
                                    return new HttpResourceNotFoundResponse();
                                }

                                return new class implements HttpResponse {
                                    public function getBody(): string
                                    {
                                        return '';
                                    }

                                    public function getHeaders(): HttpHeaders
                                    {
                                        return HttpHeaders::fromGlobalRequestHeaders();
                                    }

                                    public function getStatusCode(): int
                                    {
                                        return HttpResponse::STATUS_OK;
                                    }

                                    public function send(): void
                                    {
                                    }
                                };
                            }
                        };
                    }
                });
            }
        };
    }

    public function testReturnsHttpResponse(): void
    {
        $httpUrl = HttpUrl::fromString('http://example.com/');
        $httpHeaders = HttpHeaders::fromArray([]);
        $httpRequestBody = new HttpRequestBody('');
        $request = HttpRequest::fromParameters(HttpRequest::METHOD_GET, $httpUrl, $httpHeaders, $httpRequestBody);

        $implementationSpecificFactory = new class implements Factory{
            use FactoryTrait;
        };

        $website = $this->getIntegrationTestsWebFront($request, $implementationSpecificFactory);

        $this->assertInstanceOf(HttpResponse::class, $website->processRequest());
    }

    public function testHttpReturnsResourceNotFoundResponse(): void
    {
        $url = HttpUrl::fromString('http://example.com/non/existent/path');
        $headers = HttpHeaders::fromArray([]);
        $requestBody = new HttpRequestBody('');
        $request = HttpRequest::fromParameters(HttpRequest::METHOD_GET, $url, $headers, $requestBody);

        $implementationSpecificFactory = new class implements Factory{
            use FactoryTrait;
        };

        $website = $this->getIntegrationTestsWebFront($request, $implementationSpecificFactory);

        $this->assertInstanceOf(HttpResourceNotFoundResponse::class, $website->processRequest());
    }
}
