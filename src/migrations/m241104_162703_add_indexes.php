<?php



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
