<?php

namespace Pdsinterop\Authentication\Resource\Handler;

use Pdsinterop\Authentication\AbstractHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Authentication extends AbstractHandler
{
    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __invoke(ServerRequestInterface $request, array $args) : ResponseInterface
    {
        $response = $this->getResponse();

        $html = $this->buildResponseHtml();

        $response->getBody()->write($html);

        return $response;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * @return string
     */
    private function buildResponseHtml() : string
    {
        $html = <<<'HTML'
<h1>Authentication required</h1>

<p>In order to see the requested resource, please authenticate yourself.</p>
HTML;

        return $html;
    }
}
