<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\ScopeTrait;

class Scope implements ScopeEntityInterface
{
    use EntityTrait;
    use ScopeTrait;
}
