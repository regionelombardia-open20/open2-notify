<?php


use yii\db\Migration;

class m231108_162703_add_index_notification extends Migration
{

    const TABLE = 'notification';

    public function safeUp()
    {
        $this->createIndex('fk_notification_classname_content_id_channel', self::TABLE, ['class_name', 'content_id', 'channels']);
    }

    public function safeDown()
    {
        $this->dropIndex('fk_notification_classname_content_id_channel', self::TABLE);
    }

}
