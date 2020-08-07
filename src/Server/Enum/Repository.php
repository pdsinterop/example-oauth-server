<?php

namespace Pdsinterop\Authentication\Server\Enum;

use Pdsinterop\Authentication\Server\Repository\AccessToken;
use Pdsinterop\Authentication\Server\Repository\AuthCode;
use Pdsinterop\Authentication\Server\Repository\Client;
use Pdsinterop\Authentication\Server\Repository\RefreshToken;
use Pdsinterop\Authentication\Server\Repository\Scope;
use Pdsinterop\Authentication\Server\Repository\User;

class Repository
{
    public const ACCESS_TOKEN = AccessToken::class;
    public const AUTH_CODE = AuthCode::class;
    public const CLIENT = Client::class;
    public const REFRESH_TOKEN = RefreshToken::class;
    public const SCOPE = Scope::class;
    public const USER = User::class;

}
