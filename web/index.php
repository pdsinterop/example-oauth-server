<?php declare(strict_types=1);

/*/ Vendor /*/
use GuzzleHttp\Client as HttpClient;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use League\OAuth2\Server\CryptKey;
use League\Route\Http\Exception\HttpExceptionInterface;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;
use function MJRider\FlysystemFactory\create as createFileSystem;

/*/ Generic Pdsinterop /*/
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Pdsinterop\Authentication\Enum\ServerPrefix;
use Pdsinterop\Authentication\Enum\Time;
use Pdsinterop\Authentication\Router as ProjectRouter;

/*/ Authentication Client  /*/
use Pdsinterop\Authentication\Client\Enum\ClientOption;
use Pdsinterop\Authentication\Client\Provider;
use Pdsinterop\Authentication\Client\Router as ClientRouter;

/*/ Authentication Server /*/
use Pdsinterop\Authentication\Server\Entity\User as UserEntity;
use Pdsinterop\Authentication\Server\Enum\Repository;
use Pdsinterop\Authentication\Server\Expiration;
use Pdsinterop\Authentication\Server\Factory\GrantTypeFactory;
use Pdsinterop\Authentication\Server\Factory\AuthorizationServerFactory;
use Pdsinterop\Authentication\Server\Factory\RepositoryFactory;
use Pdsinterop\Authentication\Server\Keys;
use Pdsinterop\Authentication\Server\Repository\Client as ClientRepository;
use Pdsinterop\Authentication\Server\Router as ServerRouter;

/*/ Resource Server  /*/
use Pdsinterop\Authentication\Resource\Handler\Authentication;
use Pdsinterop\Authentication\Resource\Router as ResourceRouter;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();
ob_start();

// =============================================================================
// @TODO: Move configuration to separate object|(s)
// -----------------------------------------------------------------------------
$expires = [
    'accessToken' => Time::HOURS_1,
    'authCode' => Time::MINUTES_10,
    'refreshToken' => Time::MONTHS_1,
];

$clientSecret = 'client secret';
$clientIdentifier = 'OAuth Example App';

$encryptionKeyPath = dirname(__DIR__) . '/tests/fixtures/keys/encryption.key';
$privateKeyPath = dirname(__DIR__) . '/tests/fixtures/keys/private.key';

$grantTypes = [
    GrantType::AUTH_CODE,
    GrantType::CLIENT_CREDENTIALS,
//    GrantType::IMPLICIT,
//    GrantType::PASSWORD,
//    GrantType::REFRESH_TOKEN,
];

$location = getenv('STORAGE_ENDPOINT') ?: 'local:'.dirname(__DIR__) . '/tests/fixtures/files';
// =============================================================================


// =============================================================================
// Create Objects from I/O (filesystem, network, globals)
// -----------------------------------------------------------------------------
$encryptionKey = file_get_contents($encryptionKeyPath);

$privateKey = new CryptKey(file_get_contents($privateKeyPath));

$keys = new Keys($privateKey, $encryptionKey);

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$host = vsprintf('%s://%s%s', [
    'Scheme' => $request->getUri()->getScheme(),
    'Host' => $request->getUri()->getHost(),
    'Port' => $request->getUri()->getPort() ? ':'.$request->getUri()->getPort() : '',
]);

$isServer = strpos($request->getRequestTarget(), ServerPrefix::AUTHORIZATION) === 0;

$expiration = new Expiration($expires['accessToken'], $expires['authCode'], $expires['refreshToken']);

$user = new UserEntity();

if (isset($_SESSION['user_id'])) {
    $user->setIdentifier($_SESSION['user_id']);
}

$filesystem = createFileSystem($location);

$publicResources = [
    'public_resource.txt'
];

/*/ OAuth2 Client options /*/
$options = [
    ClientOption::CLIENT_ID => $clientIdentifier,
    ClientOption::CLIENT_SECRET => $clientSecret,
    ClientOption::REDIRECT_URI => $host . ServerPrefix::CLIENT.'/redirect-url',

    // URLs on the Authorization server
    // @FIXME: The server URLs should be gotten from a config that is also used by the server
    ClientOption::URL_AUTHORIZE => $host . ServerPrefix::AUTHORIZATION.'/authorize',
    ClientOption::URL_ACCESS_TOKEN => $host . ServerPrefix::AUTHORIZATION.'/access_token',
    ClientOption::URL_RESOURCE_OWNER_DETAILS => $host . ServerPrefix::RESOURCE.'/public_resource.txt',
];
/*/ ======================================================================== /*/


// =============================================================================
// Create factories
// -----------------------------------------------------------------------------
$repositoryFactory = new RepositoryFactory([
    Repository::CLIENT => new ClientRepository($clientIdentifier, $clientSecret,$grantTypes),
]);
$grantFactory = new GrantTypeFactory($expiration, $repositoryFactory);
$serverFactory = new AuthorizationServerFactory($repositoryFactory, $grantFactory, $keys, $expiration);
/*/ ======================================================================== /*/


// =============================================================================
// Set up routes
// -----------------------------------------------------------------------------
$response = new Response();
$router = new Router();

$client = [];
if ('this is needed in development to allow self-signed certificates') {
    $client['httpClient'] = new HttpClient(['verify' => false]);
}

$provider = new Provider($options, $client);

$routes = [
    '/' => new ProjectRouter($response),
    ServerPrefix::AUTHORIZATION => new ServerRouter($response, $serverFactory, $user, $grantTypes),
    ServerPrefix::CLIENT => new ClientRouter($response, $provider, $grantTypes),
    ServerPrefix::RESOURCE => new ResourceRouter(
        $response,
        $filesystem,
        new Authentication($response),
        $publicResources
    ),
];

array_walk($routes, static function ($handler, $route) use (&$router) {
    $router->group($route, $handler->route());
});
/*/ ======================================================================== /*/


// =============================================================================
// Create response for requested route
// -----------------------------------------------------------------------------
$statusCode = 500;
try {
    $response = $router->dispatch($request);
} catch (NotFoundException $exception) {
    $error = $exception->getMessage();
    $description = vsprintf("The requested route '%s' does not exist on this server.", [$request->getRequestTarget()]);
} catch (HttpExceptionInterface $exception) {
    $error = $exception->getMessage();
} catch (Throwable $exception) {
    // @FIXME: An exception here means a developer mistake (or "bug") as all exceptions should have been caught in the called route handler, how to handle?
    $error = 'dispatch_error';
} finally {
    if (isset($exception, $error)) {
        $message = '<h1>DISPATCH ERROR</h1><pre>' . $exception . '</pre>';

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        if ($isServer) {
            $response = $response->withHeader('content-type', 'application/json; charset=UTF-8');

            $description = $description ?? vsprintf('%s: %s', [
                    get_class($exception),
                    $exception->getMessage(),
                ]);

            $message = json_encode([
                Parameter::ERROR => $error,
                Parameter::ERROR_DESCRIPTION => $description,
                'trace' => $exception->getTrace(),
            ]);
        }

        $response->getBody()->write($message);
        $response = $response->withStatus($statusCode);
    }
}
/*/ ======================================================================== /*/


// =============================================================================
// Send the response to the browser
// -----------------------------------------------------------------------------
$emitter = new SapiEmitter();

// @FIXME: Any output means a developer mistake (or "bug"), how to handle?
$buffer = ob_get_clean();

try {
    $emitter->emit($response);
} catch (EmitterException $exception) {
    $statusCode = 500;
    // @FIXME: An exception here means a developer mistake (or "bug") as all exceptions should have been caught in the called emitter, how to handle?
    http_response_code($statusCode);
    $message = '<h1>EMITTER ERROR</h1><pre>' . $exception . '</pre>';
    if ($isServer) {
        $message = json_encode($exception);
    }
    echo $message;
}

if ($buffer !== '') {
    echo "\nOUTPUT: \n{$buffer}";
}

exit;
