<?php declare(strict_types=1);

namespace Pdsinterop\Authentication\Resource;

use Pdsinterop\Authentication\IdentifiableInterface as Entity;
use Pdsinterop\Authentication\Enum\Authorization;

class Permissions
{
    private $permissions;

    final public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Answers the question: "Does ${entity} have ${permissionType} on ${subject}?"
     *
     * @param Entity $entity
     * @param string $permissionType
     * @param string $subject
     *
     * @return bool
     */
    final public function hasAuthorization(Entity $entity, string $permissionType, string $subject): bool
    {
        $identifier = $entity->getIdentifier();

        $allowed = $this->isAllowed($identifier, $permissionType, $subject);

        return $allowed
            ? Authorization::APPROVED
            : Authorization::DENIED
        ;
    }

    private function isAllowed(string $identifier, string $permissionType, string $subject) : bool
    {
        return array_key_exists($permissionType, $this->permissions)
        && array_key_exists($subject, $this->permissions[$permissionType])
        && in_array($identifier, $this->permissions[$permissionType][$subject], true);
}
}
