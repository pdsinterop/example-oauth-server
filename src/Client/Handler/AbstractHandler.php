<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Client\Handler;

use Pdsinterop\Authentication\Client\Provider;
use Pdsinterop\Authentication\Enum\OAuth2\GrantType;
use Pdsinterop\Authentication\Enum\OAuth2\Parameter;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractHandler extends \Pdsinterop\Authentication\AbstractHandler
{
    ////////////////////////////// CLASS PROPERTIES \\\\\\\\\\\\\\\\\\\\\\\\\\\\

    /** @var Provider */
    private $provider;

    //////////////////////////// GETTERS AND SETTERS \\\\\\\\\\\\\\\\\\\\\\\\\\\

    abstract public function getGrantType() : string;

    public function getProvider() : Provider
    {
        return $this->provider;
    }

    //////////////////////////////// PUBLIC API \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    final public function __construct(ResponseInterface $response, Provider $provider)
    {
        $this->provider = $provider;

        parent::__construct($response);
    }

    final public function buildClientForm( string $title, string $content = '', string $url = '') : string
    {
        $provider = $this->getProvider();
        $grantType = $this->getGrantType();

        $parameters = $provider->getParameters();

        switch ($grantType) {
            case GrantType::AUTH_CODE:
                $parameters= [Parameter::CODE => $_SESSION['code_verifier']];
                break;

            case GrantType::CLIENT_CREDENTIALS:
                $url = $provider->getBaseAccessTokenUrl([]);
                break;
        }

        $grant = $provider->getGrantFactory()->getGrant($grantType);

        $params = $grant->prepareRequestParameters([], $parameters);

        $optionProvider = $provider->getOptionProvider();

        $options = $optionProvider->getAccessTokenOptions('POST', $params);

        $encoding = $options['headers']['content-type'] ?? 'application/x-www-form-urlencoded';

        // ---------------------------------------------------------------------
        list($uri, $query) = explode('?', $url);

        $queryParameters = '';
        if ($query !== null) {
            $queryParameters = explode('&', $query);
            natcasesort($queryParameters);

            $queryParameters = implode("\n", array_map(static function ($value) {
                [$name, $value] = explode('=', $value);

                return '<li class="url__parameter">'.$name.' = '.urldecode($value).'</li>';
            }, $queryParameters));
        }

        $body = $options['body'];

        $bodyParameters = explode('&', $body);
        natcasesort($bodyParameters);

        $bodyParameters = implode("\n", array_map(static function ($value) {
            [$name, $value] = explode('=', $value);

            return '<li class="body__parameter"><label>'.$name.' = <input type="text" name="'.$name.'" value="'.urldecode($value).'" /></label></li>';
        }, $bodyParameters));

        return <<<"HTML"
<!doctype html>
<meta charset="UTF-8">

<style>
  body {
    margin: 5% auto 0 auto;
    width: 40em;
  }

  button {
    margin: 1em 0;
    height: 2em;
    width: 100%;
  }

  title {
    display: inline;
  }

  .body__parameter,
  .url__parameter {
    list-style: none;
    white-space: nowrap;
  }

  .url__parameter:first-child::before {
    content: "?";
  }

  .url__parameter::before {
    content: "&";
    display: inline-block;
    margin-right: 0.5em;
    text-align: right
    width: 1em;
  }

  .body__parameters {
    margin-top: 1em;
  }

  .url__uri,
  .url__parameters {
    margin: 0;
  }

  .body__parameters,
  .url__parameters,
  .url__uri{
    font-family: monospace;
    font-size: 1.25em;
  }

  .body__parameter input,
  .url__parameter input {
    border: none;
    font-family: monospace;
    font-size: 1em;
    margin: 0;
    padding: 0;
    width: 35em;
  }

</style>
<body>
    <h1><title>{$title}</title></h1>

    {$content}

    <div class="url">
        <p class="url__uri">POST $uri</p>
        <ul class="url__parameters">
            {$queryParameters}
        </ul>
    </div>

    <form action="{$url}" method="post" accept-charset="UTF-8" enctype="{$encoding}">
        <ul class="body__parameters">
            {$bodyParameters}
        </ul>
        <button>Go!</button>
    </form>
</body>
HTML;
    }
}
