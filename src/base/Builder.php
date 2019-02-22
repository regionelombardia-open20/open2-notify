<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\base
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\base;

use lispa\amos\core\user\User;

/**
 * Interface Builder
 * @package lispa\amos\notificationmanager\base
 */
interface Builder
{
    /**
     * @param array $resultset
     * @param User $user
     * @return string
     */
    public function renderEmail(array $resultset, User $user);

    /**
     * @param array $resultset
     * @return string
     */
    public function getSubject(array $resultset);
}
