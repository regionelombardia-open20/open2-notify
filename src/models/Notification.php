<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notify
 * @category   CategoryName
 */


namespace open20\amos\notificationmanager\models;

use open20\amos\notificationmanager\AmosNotify;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "notification".
 *
 * @author -
 * @property integer $id
 * @property integer $user_id
 * @property integer $channels
 * @property integer $content_id
 * @property string $class_name
 * @property integer $models_content_id
 * @property integer $record_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Notification extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'channels', 'content_id', 'created_at', 'updated_at', 'models_content_id',  'record_id'], 'integer'],
            [['class_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'channels' => 'Channels',
            'content_id' => 'Content ID',
            'class_name' => 'Class Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'class' => TimestampBehavior::className(),
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationsRead()
    {
        return $this->hasMany(AmosNotify::instance()->model('NotificationsRead'), ['notification_id' => 'id']);
    }
}
