<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notify\migrations
 * @category   CategoryName
 */

use open20\amos\notificationmanager\models\Newsletter;
use yii\db\Migration;

/**
 * Class m211108_151140_add_notify_newsletter_field
 */
class m211108_151140_add_notify_newsletter_field extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(Newsletter::tableName(), 'programmed_send_date_time', $this->dateTime()->null()->defaultValue(null)->after('subject'));
        return true;
    }
    
    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(Newsletter::tableName(), 'programmed_send_date_time');
        return true;
    }
}
