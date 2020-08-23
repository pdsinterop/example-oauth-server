<?php declare(strict_types=1);

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

        $html = $this->buildResponseHtml($args['path']);

        $response->getBody()->write($html);

        return $response;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function buildResponseHtml(string $resource) : string
    {
        return <<<"HTML"
<h1>Authentication required</h1>

<p>In order to see the requested resource "{$resource}", you need to be authenticated.</p>

<p>As this example is built to help you understand how things work, this screen is shown.</p>

<p>Under normal circumstances, this screen would immediately redirect to the Authorization Server</p>

<p>Pick one of the methods below to authenticate:</p>

<ul>
    <li><b>@FIXME: </b> Show authentication methods here</li>
</ul>

HTML;
    }
}
