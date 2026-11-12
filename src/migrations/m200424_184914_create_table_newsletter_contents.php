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

/**
 * Class m200424_184914_create_table_newsletter_contents
 */
class m200424_184914_create_table_newsletter_contents extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%notification_newsletter_contents}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'newsletter_id' => $this->integer()->notNull()->comment('Newsletter Id'),
            'newsletter_contents_conf_id' => $this->integer()->notNull()->comment('Newsletter Contents Conf Id'),
            'content_id' => $this->integer()->notNull()->comment('Content Id'),
            'order' => $this->smallInteger()->unsigned()->notNull()->comment('Order'),
        ];
        $this->setAddCreatedUpdatedFields(true);
    }

    /**
     * @inheritdoc
     */
    protected function beforeTableCreation()
    {
        parent::beforeTableCreation();
        $this->setAddCreatedUpdatedFields(true);
    }

    /**
     * @inheritdoc
     */
    protected function afterTableCreation()
    {
        $this->createIndex('newsletter_contents_all_index', $this->tableName, ['newsletter_id', 'newsletter_contents_conf_id', 'content_id']);
        $this->createIndex('newsletter_contents_index', $this->tableName, ['newsletter_id', 'newsletter_contents_conf_id']);
        $this->createIndex('newsletter_index', $this->tableName, ['newsletter_id']);
    }

    /**
     * @inheritdoc
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey('fk_newsletter_contents_newsletter', $this->tableName, 'newsletter_id', 'notification_newsletter', 'id');
        $this->addForeignKey('fk_newsletter_contents_newsletter_contents_conf', $this->tableName, 'newsletter_contents_conf_id', 'notification_newsletter_contents_conf', 'id');
    }
}
