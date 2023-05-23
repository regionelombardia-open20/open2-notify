<?php

use yii\db\Migration;

class m221202_164700_add_fields_notification_schedule extends Migration
{
    const TABLE = 'notification_schedule';

    public function safeUp()
    {
       $this->addColumn(self::TABLE, 'max_user_id_to_notify' ,$this->integer()->defaultValue(null)->after('last_notified_user_id'));

    }

    public function safeDown()
    {
        $this->dropColumn(self::TABLE, 'max_user_id_to_notify' );
    }
}
