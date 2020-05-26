<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notificationmanager\models
 * @category   CategoryName
 */

namespace open20\amos\notificationmanager\models;

use open20\amos\notificationmanager\AmosNotify;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class NotificationConf
 *
 * This is the model class for table "notificationconf".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $email
 * @property integer $sms
 * @property integer $notifications_enabled
 * @property integer $notify_content_pubblication
 * @property integer $notify_comments
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\core\user\User $user
 *
 * @package open20\amos\notificationmanager\models
 */
class NotificationConf extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notificationconf';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['user_id', 'email', 'sms', 'notifications_enabled', 'notify_content_pubblication', 'notify_comments', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['user_id'], 'required'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosNotify::t('amosnotify', 'ID'),
            'user_id' => AmosNotify::t('amosnotify', 'User ID'),
            'email' => AmosNotify::t('amosnotify', 'Email'),
            'sms' => AmosNotify::t('amosnotify', 'Sms'),
            'created_at' => AmosNotify::t('amosnotify', 'Created At'),
            'updated_at' => AmosNotify::t('amosnotify', 'Updated At'),
            'deleted_at' => AmosNotify::t('amosnotify', 'Deleted At'),
            'created_by' => AmosNotify::t('amosnotify', 'Created By'),
            'updated_by' => AmosNotify::t('amosnotify', 'Updated By'),
            'deleted_by' => AmosNotify::t('amosnotify', 'Deleted By')
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            "TimestampBehavior" => [
                'class' => TimestampBehavior::className(),
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\open20\amos\core\user\User::className(), ['id' => 'user_id']);
    }
}
