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

use yii\db\Migration;

class m171127_174040_popolate_notification_send_email extends Migration
{
    const TABLE = 'notification_send_email';

    public function safeUp()
    {
       $this->execute("
       INSERT INTO notification_send_email (classname, content_id, created_at, updated_at, created_by, updated_by)
SELECT DISTINCT class_name as classname, content_id, NOW(), NOW(), 1, 1
FROM notification
INNER JOIN notificationread ON (notification.id = notificationread.notification_id)
WHERE notification.channels = 1;

       ");
        return true;
    }

    public function safeDown()
    {

        return true;
    }
}
