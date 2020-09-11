<?php

namespace Pdsinterop\Authentication\Enum;

class Scopes extends AbstractEnum
{
    public const OFFLINE_ACCESS = 'offline_access';
    public const OPEN_ID = 'openid';
    public const PROFILE = 'profile';
    public const WEB_ID = 'webid';

    // @CHECKME: chat scope?
}
