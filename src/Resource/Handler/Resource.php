<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Resource\Handler;

use League\Flysystem\Filesystem;
use Pdsinterop\Authentication\AbstractHandler;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * In order to visit a resource, a user needs to be authenticated.
 *
 * This can be done with any of the OAuth2 grant types supported by the server
 * (listed under the `/client` URI).
 *
 * If the user is authorized, the authorization method/token/etc. are be shown.
 *
 * Otherwise the user is redirected to a screen asking them for authorization.
 *
 * The resource is returned as JSON.
 */
class Resource extends AbstractHandler
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private const KEY_AUTHENTICATE = 'authenticate';

    /** @var Authentication */
    private $authentication;
    /** @var Filesystem */
    private $filesystem;
    /** @var string[] */
    private $publicFiles;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function getPublicFiles() : array
    {
        return $this->publicFiles;
    }

    /**
     * @param string[] $publicFiles
     */
    public function setPublicResources(array $publicFiles) : void
    {
        $this->publicFiles = $publicFiles;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(Response $response, Filesystem $filesystem, Authentication $authentication)
    {
        $this->authentication = $authentication;
        $this->filesystem = $filesystem;

        parent::__construct($response);
    }

    public function __invoke(Request $request, array $args) : Response
    {
        $filesystem = $this->filesystem;
        $response = $this->getResponse();

        $queryParams = $request->getQueryParams();
        $path = $args['path'];

        $showAuthentication = array_key_exists(self::KEY_AUTHENTICATE, $queryParams);
        $showFile = $this->isAuthorized() || $this->isPublicFile($path);

        if ($showAuthentication) {
            $authenticationHandler = $this->authentication;
            $authenticationHandler($request, $args);
        } elseif ($showFile) {
            if ($filesystem->has($path)) {
                $resource = $filesystem->read($path);
                $response = $this->buildResourceResponse($resource);
            } else {
                $response = $this->buildNotExistResponse($request);
            }
        } else {
            $url = rtrim($request->getRequestTarget(), '/');
            $response = $response->withHeader('Location', $url . '?' . self::KEY_AUTHENTICATE)->withStatus(302);
        }

        return $response;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    private function isAuthorized() : bool
    {
        return false;
    }

    private function isPublicFile($path) : bool
    {
        return in_array($path, $this->getPublicFiles(), true);
    }

    private function buildNotExistResponse(Request $request) : Response
    {
        $description = vsprintf(
            'The requested resource "%s" does not exist on this server.',
            [$request->getRequestTarget()]
        );

        $response = $this->createJsonResponse([
            Parameter::ERROR => 'Not Found',
            Parameter::ERROR_DESCRIPTION => $description,
        ]);

        return $response->withStatus(404);
    }

    private function buildResourceResponse(string $resource) : Response
    {
        $response = $this->createJsonResponse([
            Parameter::RESOURCE => base64_encode($resource),
        ]);

        return $response->withHeader('content-type', 'application/json; charset=UTF-8');
    }
}
