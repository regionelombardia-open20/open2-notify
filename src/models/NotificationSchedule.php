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

namespace open20\amos\notificationmanager\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "notification_schedule".
 */
class NotificationSchedule extends \open20\amos\notificationmanager\models\base\NotificationSchedule
{
    const STATUS_WORKING = 'working';
    const STATUS_DONE = 'done';
    const STATUS_ERROR = 'error';

    public function representingColumn()
    {
        return [
//inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints()
    {
        return [
        ];
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
                ]);
    }


    public function getEditFields()
    {
        $labels = $this->attributeLabels();

        return [
            [
                'slug' => 'status',
                'label' => $labels['status'],
                'type' => 'string'
            ],
            [
                'slug' => 'type',
                'label' => $labels['type'],
                'type' => 'integer'
            ],
            [
                'slug' => 'last_notified_user_id',
                'label' => $labels['last_notified_user_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'ended_at',
                'label' => $labels['ended_at'],
                'type' => 'datetime'
            ],
        ];
    }

    /**
     * @return string marker path
     */
    public function getIconMarker()
    {
        return null; //TODO
    }

    /**
     * If events are more than one, set 'array' => true in the calendarView in the index.
     * @return array events
     */
    public function getEvents()
    {
        return NULL; //TODO
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent()
    {
        return NULL; //TODO e.g. Yii::$app->urlManager->createUrl([]);
    }

    /**
     * @return color event
     */
    public function getColorEvent()
    {
        return NULL; //TODO
    }

    /**
     * @return title event
     */
    public function getTitleEvent()
    {
        return NULL; //TODO
    }

    /**
     * @param $arrayNotifications
     * @param $arrayNotificationComments
     * @return NotificationSchedule
     */
    public static function createSchedule($arrayNotifications, $arrayNotificationComments, $type, $maxUserIdToSend, $segmentationInfos)
    {

        $schedule = new NotificationSchedule();
        $schedule->type = $type;
        $schedule->max_user_id_to_notify = $maxUserIdToSend;
        if(empty($maxUserIdToSend)){
            $schedule->status = NotificationSchedule::STATUS_DONE;
        }else{
            $schedule->status = NotificationSchedule::STATUS_WORKING;
        }
        if(isset($segmentationInfos['offset'])){
            $schedule->start_user_id =  $segmentationInfos['offset'];
        }
        $schedule->save(false);
        foreach ($arrayNotifications as $notification) {
            $notifyContent = new NotificationScheduleContent();
            $notifyContent->notification_schedule_id = $schedule->id;
            $notifyContent->notification_id = $notification['id'];
            $notifyContent->classname = $notification['class_name'];
            $notifyContent->save(false);
        }
        foreach ($arrayNotificationComments as $notificationComment) {
            $notifyContent = new NotificationScheduleContent();
            $notifyContent->notification_schedule_id = $schedule->id;
            $notifyContent->notification_id = $notificationComment['id'];
            $notifyContent->classname = $notificationComment['class_name'];
            $notifyContent->save(false);
        }
        return $schedule;
    }

    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function notificationScheduled()
    {
        $notificationSchedule = NotificationSchedule::find()->andWhere(['status' => NotificationSchedule::STATUS_WORKING])->one();
        return $notificationSchedule;
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isLastUserToSend($user_id){
            return $this->max_user_id_to_notify == $user_id;
    }
}
