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

class m241104_162703_add_indexes extends Migration
{

    const TABLE = 'notification';
    const TABLE_READ = 'notificationread';

    public function safeUp()
    {

        $this->createIndex('idx_notificationread_composite', self::TABLE_READ, ['notification_id', 'notification_type', 'user_id']);
        $this->createIndex('idx_notification_composite', self::TABLE, ['channels', 'created_at', 'class_name']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_notificationread_composite', self::TABLE_READ);
        $this->dropIndex('idx_notification_composite', self::TABLE);
    }

}
