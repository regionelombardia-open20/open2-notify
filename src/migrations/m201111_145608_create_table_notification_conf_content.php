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

use open20\amos\core\migration\AmosMigrationTableCreation;

use yii\db\Migration;
use yii\db\Schema;

class m201111_145608_create_table_notification_conf_content extends Migration
{

    const TABLE = 'notification_conf_content';


    /**
     * @inheritdoc
     */
    public function up()
    {


        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'notification_conf_id' => $this->integer()->defaultValue(null)->comment('Notification conf'),
                'models_classname_id' => $this->integer()->defaultValue(null)->comment('Content classname'),
                'content_id' => $this->integer()->defaultValue(null)->comment('Content id'),
                'email' => $this->integer(1)->defaultValue(1)->comment('Enable email'),
                'push_notification' => $this->integer(1)->defaultValue(1)->comment('Enable email'),
                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' => $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' => $this->integer()->comment('Created by'),
                'updated_by' => $this->integer()->comment('Updated at'),
                'deleted_by' => $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);

            $this->addForeignKey('fk_notification_conf_content_notification_content_id', self::TABLE, 'notification_conf_id', 'notificationconf', 'id');
            $this->addForeignKey('fk_notification_conf_content_models_class_id', self::TABLE, 'models_classname_id', 'models_classname', 'id');

        } else {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }


    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropTable('notification_conf_content');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
