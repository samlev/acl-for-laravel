<?php

namespace SamLev\Acl\Permissions;

use SamLev\Acl\Support\Mechanisms\Data\Fromable;
use SamLev\Acl\Support\Mechanisms\Data\Toable;

abstract class BasePermission
{
    use Fromable;
    use Toable;
}
