<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Pdsinterop\Authentication\IdentifiableInterface;

class User implements UserEntityInterface, IdentifiableInterface
{
    use EntityTrait;
}
