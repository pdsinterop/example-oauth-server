<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Handler;

use Pdsinterop\Authentication\AbstractHandler;
use Pdsinterop\Authentication\Enum\ServerPrefix;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomepageHandler extends AbstractHandler
{
    public function __invoke(Request $request, array $args) : Response
    {
        $response = $this->getResponse();

        $prefix = [
            'client' => ServerPrefix::CLIENT,
            'resource' => ServerPrefix::RESOURCE,
            'server' => ServerPrefix::AUTHORIZATION,
        ];

        $response->getBody()->write(<<<"HTML"
            <h1>Hello world!</h1>

            <p>
                Some dummy resources have been added, to complete the example.
            </p>

            <p>The following URLs can be visited:</p>

            <ul>
                <li><a href="/">/</a> The page you find yourself on now.</li>
                <li><a href="{$prefix['client']}">{$prefix['client']}</a> An overview of OAuth <strong>client</strong> URLs that can be visited.</li>
                <li>
                    <a href="{$prefix['resource']}">{$prefix['resource']}</a>
                    Example <strong>resources</strong> to trigger the
                    authentication process using the server and client URLs.
                </li>
            </ul>
HTML
        );

        return $response->withStatus(200);
    }
}
