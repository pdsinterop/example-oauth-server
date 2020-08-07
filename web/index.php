<?php declare(strict_types=1);

/*/ Vendor /*/
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Exception\EmitterException;
use League\Route\Http\Exception\HttpExceptionInterface;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Router;

/*/ Generic Pdsinterop /*/
use Pdsinterop\Authentication\Router as ProjectRouter;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();
ob_start();

$clientIdentifier = 'PDS Interop OAuth Example App';

$request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$host = vsprintf('%s://%s%s', [
    'Scheme' => $request->getUri()->getScheme(),
    'Host' => $request->getUri()->getHost(),
    'Port' => $request->getUri()->getPort() ? ':'.$request->getUri()->getPort() : '',
]);


// Set up routes
$response = new Response();
$router = new Router();

$routes = [
    '/' => new ProjectRouter($response),
];

array_walk($routes, function ($handler, $route) use (&$router) {
    $router->group($route, $handler->route());
});

// Create response for requested route
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
    if (isset($exception)) {
        $message = '<h1>DISPATCH ERROR</h1><pre>' . $exception . '</pre>';
        $statusCode = 500;

        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        }

        $response->getBody()->write($message);
        $response = $response->withStatus($statusCode);
    }
}


// Send the response to the browser
$emitter = new SapiEmitter();

// Any output means a developer mistake (or "bug")
ob_clean();

try {
    $emitter->emit($response);
} catch (EmitterException $exception) {
    // @FIXME: An exception here means a developer mistake (or "bug") as all exceptions should have been caught in the called emitter, how to handle?
    http_response_code(500);
    $message = '<h1>EMITTER ERROR</h1><pre>' . $exception . '</pre>';

    echo $message;
}

exit;
