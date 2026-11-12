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

namespace open20\amos\notificationmanager\models\base;

use open20\amos\core\user\User;
use Yii;

/**
 * This is the base-model class for table "notification_schedule".
 *
 * @property integer $id
 * @property string $status
 * @property integer $type
 * @property integer $max_user_id_to_notify
 * @property integer $last_notified_user_id
 * @property string $ended_at
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @property integer $start_user_id
 *
 * @property \open20\amos\notificationmanager\models\User $lastNotifiedUser
 * @property \open20\amos\notificationmanager\models\NotificationScheduleContent[] $notificationScheduleContents
 */
class  NotificationSchedule extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'last_notified_user_id', 'created_by', 'updated_by', 'deleted_by','max_user_id_to_notify'], 'integer'],
            [['start_user_id','ended_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['last_notified_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_notified_user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosnotify', 'ID'),
            'status' => Yii::t('amosnotify', 'Status'),
            'type' => Yii::t('amosnotify', 'Type'),
            'last_notified_user_id' => Yii::t('amosnotify', 'Content classname'),
            'ended_at' => Yii::t('amosnotify', 'Ended at'),
            'created_at' => Yii::t('amosnotify', 'Created at'),
            'updated_at' => Yii::t('amosnotify', 'Updated at'),
            'deleted_at' => Yii::t('amosnotify', 'Deleted at'),
            'created_by' => Yii::t('amosnotify', 'Created by'),
            'updated_by' => Yii::t('amosnotify', 'Updated at'),
            'deleted_by' => Yii::t('amosnotify', 'Deleted at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastNotifiedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'last_notified_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationScheduleContents()
    {
        return $this->hasMany(\open20\amos\notificationmanager\models\NotificationScheduleContent::className(), ['notification_schedule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(\open20\amos\notificationmanager\models\Notification::className(), ['id' => 'notification_id'])->via('notificationScheduleContents');
    }
}
