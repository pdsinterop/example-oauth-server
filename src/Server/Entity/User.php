<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Server\Entity;

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class User implements UserEntityInterface
{
    use EntityTrait;
}
