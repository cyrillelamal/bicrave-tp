<?php

namespace App\Security;

/**
 * @todo PHP8.1 enum
 */
class Role
{
    public const PREFIX = 'ROLE_';

    public const USER = self::PREFIX . 'USER';
    public const CUSTOMER = self::PREFIX . 'CUSTOMER';

    public const CONTENT_MANAGER = self::PREFIX . 'CONTENT_MANAGER';

    public const DEFAULT_ROLES = [
        self::USER
    ];
}
