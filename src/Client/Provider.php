<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client;

use GuzzleHttp\Exception\BadResponseException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class Provider extends GenericProvider
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'resource';

    /** @var array */
    private $options;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function getParameters() : array
    {
        $parameters = $this->getAuthorizationParameters($this->options);

        return array_filter($parameters, static function ($parameter) {
            return Parameter::has($parameter);
        }, ARRAY_FILTER_USE_KEY);
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function __construct(array $options = [], array $collaborators = [])
    {
        $this->options = $this->convertOptions($options);

        parent::__construct($options, $collaborators);
    }

    public function getAccessToken($grant, array $options = []) : AccessTokenInterface
    {
        return parent::getAccessToken($grant, $options);
    }

    /**
     * Sends a request and returns the parsed response.
     *
     * @param RequestInterface $request
     *
     * @return mixed
     * @throws IdentityProviderException
     */
    public function getParsedResponse(RequestInterface $request)
    {
        // @CHECKME: Add error to JSON handling? Or handle higher up and output json there?
        try {
            $response = $this->getResponse($request);
        } catch (BadResponseException $exception) {
            throw $exception;
            $response = $exception->getResponse();
            $body = $response->getBody();

            $json = $body->getContents();
            $body->rewind();
            $body->write('{"error" : '.$json.'}');
        }

        $parsed = $this->parseResponse($response);

        $this->checkResponse($response, $parsed);

        return $parsed;
    }

    ////////////////////////////// UTILITY METHODS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /**
     * Parses the response according to its content-type header.
     *
     * @param ResponseInterface $response
     *
     * @return string|array
     * @throws UnexpectedValueException
     */
    protected function parseResponse(ResponseInterface $response)
    {
        $content = (string) $response->getBody();
        $type = $this->getContentType($response);

        if (strpos($type, 'urlencoded') !== false) {
            parse_str($content, $parsed);

            return $parsed;
        }

        // Attempt to parse the string as JSON regardless of content type,
        // since some providers use non-standard content types. Only throw an
        // exception if the JSON could not be parsed when it was expected to.
        try {
            return $this->parseJson($content);
        } catch (UnexpectedValueException $exception) {

echo'<pre style="border: 1px solid red;">'.
htmlentities(var_export($content, true));
echo '</pre>';

            if (strpos($type, 'json') !== false) {
                throw $exception;
            }

            if ($response->getStatusCode() === 500) {
                throw new UnexpectedValueException(
                    'An OAuth server error was encountered that did not contain a JSON body',
                    0,
                    $exception
                );
            }

            return $content;
        }
    }

//    final protected function getAuthorizationParameters(array $options)
//    {
//        return parent::getAuthorizationParameters($options);
//    }
//

    private function convertOptions(array $options) : array
    {
        $keys = array_map(static function ($key) {
            // Convert key name from camelcase to lowercase
            return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $key));
        }, array_keys($options));

        return array_combine($keys, array_values($options));
    }
}
