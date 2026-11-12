<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notify
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
use yii\db\Schema;


class m170209_173549_init_notify extends Migration
{
    const TABLE = '{{%notification}}';
    const TABLE_READ = '{{%notificationread}}';
    
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_INTEGER . " DEFAULT NULL",
                'channels' => Schema::TYPE_INTEGER . " DEFAULT NULL",
                'content_id' =>Schema::TYPE_INTEGER . " DEFAULT NULL",
                'class_name' => Schema::TYPE_STRING . "(255) DEFAULT NULL",
                'created_at' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL ",
                'updated_at' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL ",
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->createIndex("idx_1", self::TABLE, ["user_id","content_id"]);
           
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }
        
        if ($this->db->schema->getTableSchema(self::TABLE_READ, true) === null)
        {
            $this->createTable(self::TABLE_READ, [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_INTEGER . " DEFAULT NULL",
                'notification_id' => Schema::TYPE_INTEGER . " DEFAULT NULL",
                'created_at' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Creato il'",
                'updated_at' => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Aggiornato il'",
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
           $this->createIndex("idx_1", self::TABLE_READ, ["user_id","notification_id"]);
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }
        
        return true;
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) !== null)
        {
            $this->dropTable(self::TABLE);
        }
        else
        {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }
        
        if ($this->db->schema->getTableSchema(self::TABLE_READ, true) !== null)
        {
            $this->dropTable(self::TABLE_READ);
        }
        else
        {
            echo "Nessuna cancellazione eseguita in quanto la tabella non esiste";
        }
        
        return true;
    }
        
}
