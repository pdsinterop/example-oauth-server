<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Debug extends AbstractHandler
{
    public function getGrantType() : string
    {
        return '';
    }

    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        $response = $this->getResponse();

        $content = '' .
            '<h2>$_SESSION</h2><pre><code>' . var_export($_SESSION, true) . "</code></pre>\n" .
            '<h2>Cookie()</h2><pre><code>' . var_export($request->getCookieParams(), true) . "</code></pre>\n" .
            '<h2>Query Params</h2><pre><code>' . var_export($request->getQueryParams(), true) . "</code></pre>\n" .
            '<h2>Headers</h2><pre><code>' . var_export($request->getHeaders(), true) . "</code></pre>\n" .
            '<h2>Body Contents</h2><pre><code>' . var_export($request->getBody()->getContents(), true) . "</code></pre>\n" .
            '<h2>Attributes</h2><pre><code>' . var_export($request->getAttributes(), true) . "</code></pre>\n" .
            ''
        ;

        $response->getBody()->write($content);

        return $response;
    }
}
