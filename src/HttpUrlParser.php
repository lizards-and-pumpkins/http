<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\Http;

interface HttpUrlParser
{
    public function getPath(HttpUrl $url): string;
}
