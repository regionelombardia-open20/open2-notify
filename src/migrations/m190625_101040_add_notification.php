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

class m190625_101040_add_notification extends Migration
{
    const TABLE = 'notification';

    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'record_id' ,$this->integer()->defaultValue(null)->after('class_name')->comment('Record id'));
        $this->addColumn(self::TABLE, 'models_classname_id' ,$this->integer()->defaultValue(null)->after('class_name')->comment('Scope pubblication'));
        $this->addForeignKey('fk_notification_models_classname_id', self::TABLE, 'models_classname_id', 'models_classname', 'id');
    }

    public function safeDown()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS =0");
        $this->addColumn(self::TABLE, 'record_id' ,$this->integer()->defaultValue(null)->after('class_name')->comment('Recordi id'));
        $this->addColumn(self::TABLE, 'models_classname_id' ,$this->integer()->defaultValue(null)->after('class_name')->comment('Scope pubblication'));
        $this->execute('SET FOREIGN_KEY_CHECKS = 1');

    }
}
