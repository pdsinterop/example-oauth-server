<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Exception;

use Throwable;

class UnauthorizedException extends \Exception
{
    public function __construct($path , $code = 0, Throwable $previous = null)
    {
        $message = vsprintf('Not authorized for resource "%s"', [
            $path
        ]);
        parent::__construct($message, $code, $previous);
    }
}
