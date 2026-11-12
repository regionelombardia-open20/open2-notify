<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\events\migrations
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
use open20\amos\notificationmanager\rules\DeleteOwnNewsletterRule;
use open20\amos\notificationmanager\rules\UpdateOwnNewsletterRule;
use open20\amos\notificationmanager\widgets\icons\WidgetIconNewsletterAll;
use open20\amos\notificationmanager\widgets\icons\WidgetIconNewsletterCreatedBy;
use open20\amos\notificationmanager\widgets\icons\WidgetIconNewsletterDashboard;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

/**
 * Class m200428_162835_newsletter_permissions
 */
class m200428_162835_newsletter_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return ArrayHelper::merge(
            $this->setPluginRoles(),
            $this->setModelPermissions(),
            $this->setWidgetsPermissions()
        );
    }
    
    private function setPluginRoles()
    {
        return [
            [
                'name' => 'NOTIFY_ADMINISTRATOR',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Administrator role for notify plugin',
                'parent' => ['ADMIN']
            ],
            [
                'name' => 'NEWSLETTER_ADMINISTRATOR',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Administrator role for newsletter',
                'parent' => ['NOTIFY_ADMINISTRATOR']
            ],
            [
                'name' => 'NEWSLETTER_MANAGER',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Manager role for newsletter',
                'parent' => ['NEWSLETTER_ADMINISTRATOR']
            ]
        ];
    }
    
    private function setModelPermissions()
    {
        return [
            [
                'name' => 'NEWSLETTER_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Create permission for model Newsletter',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => 'NEWSLETTER_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Read permission for model Newsletter',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => 'NEWSLETTER_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Update permission for model Newsletter',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', UpdateOwnNewsletterRule::className()]
            ],
            [
                'name' => 'NEWSLETTER_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Delete permission for model Newsletter',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', DeleteOwnNewsletterRule::className()]
            ],
            [
                'name' => UpdateOwnNewsletterRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Rule to update own newsletters',
                'ruleName' => UpdateOwnNewsletterRule::className(),
                'parent' => ['NEWSLETTER_MANAGER']
            ],
            [
                'name' => DeleteOwnNewsletterRule::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Rule to delete own newsletters',
                'ruleName' => DeleteOwnNewsletterRule::className(),
                'parent' => ['NEWSLETTER_MANAGER']
            ],
        ];
    }
    
    private function setWidgetsPermissions()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';
        return [
            [
                'name' => WidgetIconNewsletterDashboard::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetIconNewsletterDashboard',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => WidgetIconNewsletterAll::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetIconNewsletterAll',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
            [
                'name' => WidgetIconNewsletterCreatedBy::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetIconNewsletterCreatedBy',
                'parent' => ['NEWSLETTER_ADMINISTRATOR', 'NEWSLETTER_MANAGER']
            ],
        ];
    }
}
