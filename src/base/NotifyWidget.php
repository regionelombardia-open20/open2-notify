<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-notify
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

namespace open20\amos\notificationmanager\base;


interface NotifyWidget
{

    /**
     * @param $uid
     * @param $class_name
     * @param null $externalquery
     * @param NotificationChannels $channel
     */
    public function notificationOff($uid, $class_name, $externalquery = null, $channel = 0x0001);

    /**
     * @param $uid
     * @param $class_name
     * @param null $externalquery
     * @param NotificationChannels $channel
     */
    public function notificationOn($uid, $class_name, $externalquery = null, $channel = 0x0001);

    /**
     * @param $uid
     * @param $class_name
     * @param null $externalquery
     */
    public function countNotRead($uid, $class_name, $externalquery = null);

    /**
     * @param $model
     * @param null $uid
     * @return bool
     */
    public function modelIsRead($model, $uid = null);

    /**
     * @param string $modelClassName
     * @param string $type
     * @return array
     */
    public static function manageNewChannelNotifications($modelClassName, $channel, $type);

    /**
     * @param int $uid
     * @param string $class_name
     * @param int $contentId
     * @return bool
     */
    public function favouriteOn($uid, $class_name, $contentId);

    /**
     * @param int $uid
     * @param string $class_name
     * @param int $contentId
     * @return bool
     */
    public function favouriteOff($uid, $class_name, $contentId);

    /**
     * @param $model
     * @param null $uid
     * @return bool
     */
    public function isFavorite($model, $uid = null);
}