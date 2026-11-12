<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notify\migrations
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

use open20\amos\core\migration\AmosMigrationPermissions;
use open20\amos\notificationmanager\models\Newsletter;
use yii\rbac\Permission;

/**
 * Class m200508_163240_newsletter_workflow_permissions
 */
class m200508_163240_newsletter_workflow_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => Newsletter::WORKFLOW_STATUS_DRAFT,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso stato workflow Newsletter: Bozza',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => Newsletter::WORKFLOW_STATUS_WAIT_SEND,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso stato workflow Newsletter: In attesa di invio',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => Newsletter::WORKFLOW_STATUS_WAIT_RESEND,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso stato workflow Newsletter: In attesa di reinvio',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => Newsletter::WORKFLOW_STATUS_SENT,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso stato workflow Newsletter: Inviata',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ]
        ];
    }
}
