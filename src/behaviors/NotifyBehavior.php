<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notify
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\behaviors;

use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\models\NotificationChannels;
use \lispa\amos\core\controllers\CrudController;
use lispa\amos\notificationmanager\models\NotificationSendEmail;
use ReflectionClass;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\log\Logger;
use yii\web\Application;

/**
 * Class NotifyBehavior
 * @package lispa\amos\core\behaviors
 */
class NotifyBehavior extends Behavior {

    public static $EVENT_METHOD_EVALUATE = 'evaluatenotify';
    public static $EVENT_METHOD_READED = 'notifyreaded';
    public static $EVENT_METHOD_READED_DETAIL = 'notifyreadeddetail';
    private $events = [];
    private $channels = [];
    private $conditions = [];

    public $saveNotificationSendEmail;

    /**
     * @var array $mailStatuses
     */
    private $mailStatuses = [];

    /**
     * 
     */
    public function init() {
        $this->notificationInit();
        parent::init();
    }

    /**
     * 
     */
    private function notificationInit() {
        $this->initNotifyType();
        $this->initNotifyEvents();
    }

    /**
     * 
     */
    private function initNotifyType() {
        $channels = $this->getChannels();
        if (empty($channels)) {
            $this->setChannels([NotificationChannels::CHANNEL_ALL]);
        }
    }

    /**
     * 
     */
    private function initNotifyEvents() {
        $events = $this->getEvents();
        if (empty($events)) {
            $this->setEvents([
                ActiveRecord::EVENT_AFTER_INSERT,
                ActiveRecord::EVENT_AFTER_UPDATE,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function events() {
        $eventsList = [ /*ActiveRecord::EVENT_AFTER_FIND => self::$EVENT_METHOD_READED,*/
            CrudController::AFTER_FINDMODEL_EVENT => self::$EVENT_METHOD_READED_DETAIL];
        foreach ($this->events as $event) {
            $eventsList[$event] = self::$EVENT_METHOD_EVALUATE;
        }
        return $eventsList;
    }

    
    /**
     * 
     * @param array $events
     */
    public function setEvents(array $events) {
        $this->events = $events;
    }

    /**
     * 
     * @return array
     */
    public function getEvents() {
        return $this->events;
    }
    
    /**
     * 
     * @param array $array
     */
    public function setChannels($array) {
        $this->channels = $array;
    }

    /**
     * 
     * @return array
     */
    public function getChannels() {
        return $this->channels;
    }
    
    /**
     *
     * @param array $conditions
     */
    public function setConditions($conditions) {
        $this->conditions = $conditions;
    }

    /**
     * @return array
     */
    public function getMailStatuses()
    {
        return $this->mailStatuses;
    }

    /**
     * @param array $mailStatuses
     */
    public function setMailStatuses($mailStatuses)
    {
        $this->mailStatuses = $mailStatuses;
    }

    /**
     * 
     * @param array $event
     */
    public function evaluatenotify($event) {
        try {
            $model = $event->sender;
            if ($this->isNotify($model) === false) {
                return;
            }
            $this->notify($event);
        } catch (Exception $bex) {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * 
     * @param array $event
     */
    private function notify($event) {
        try {
            $model = $event->sender;
            $this->persistNotify($model);
        } catch (Exception $bex) {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * 
     * @param array $event
     */
    public function notifyreaded($event) {
        try {
            $model = $event->sender;
            $this->persistNotifyReaded($model);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
    }
    
    /**
     * 
     * @param array $event
     */
    public function notifyreadeddetail($event) {
        try {
            $model = $event->sender;
            $this->persistNotifyReaded($model,NotificationChannels::CHANNEL_READ_DETAIL);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * Method to persist Notification
     * 
     * @param array $model
     */
    private function persistNotify($model) {
        try {
            $notifyWidget = AmosNotify::instance();
            if($notifyWidget != null){
                foreach ($this->evaluateChannels() as $channel) {
                    if($notifyWidget->confirmEmailNotification) {
                        //used for the modal to choose if you want to send the email notification
                        $this->saveNotificationSendEmail(get_class($model), $channel, $model->getAttributes()[$model->primaryKey()[0]]);
                    }
                    $notify = $notifyWidget->createModel('Notification')->findOne(['content_id' => $model->getAttributes()[$model->primaryKey()[0]],
                    'channels' => $channel, 'class_name' => get_class($model) ]);
                     if($notify == null){
                        $notify = $notifyWidget->createModel('Notification');
                        if(\Yii::$app instanceof Application) {
                            $notify->user_id = Yii::$app->user->id;
                        }else{
                            $notify->user_id = 0;
                        }
                        $notify->content_id = $model->getAttributes()[$model->primaryKey()[0]];
                        $notify->channels = $channel;
                        $notify->class_name = get_class($model);
                     }else{
                         $notify->updated_at = null;
                     }
                    $notify->save();
                }
            }
        } catch (Exception $bex) {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * Method to persist NotificationsRead.
     * 
     * @param array $model
     * @param integer $channel
     */
    private function persistNotifyReaded($model,$channel = NotificationChannels::CHANNEL_READ) {

        try {
             $notifyWidget = AmosNotify::instance();
            if($notifyWidget != null){
                $notify = $notifyWidget->createModel('Notification')->findOne(['content_id' => $model->getAttributes()[$model->primaryKey()[0]],
                    'channels' => $channel, 'class_name' => get_class($model) ]);
                if ($notify) {
                    $notify_read = $notifyWidget->createModel('NotificationsRead');
                    $notify_load = $notify_read->findOne(['user_id' => Yii::$app->user->id, 'notification_id' => $notify->id]);
                    if ($notify_load) {
                        $notify_read = $notify_load;
                    }
                    $notify_read->user_id = Yii::$app->user->id;
                    $notify_read->notification_id = $notify->id;
                    $notify_read->updated_at = null;
                    $notify_read->save();
                }
            }
        } catch (Exception $bex) {
            Yii::getLogger()->log($bex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * 
     * @return array
     */
    private function evaluateChannels() {

        if (in_array(NotificationChannels::CHANNEL_ALL, $this->channels)) {
            $refl = new ReflectionClass(NotificationChannels::className());
            return $refl->getConstants();
        }
        return $this->channels;
    }

    /**
     * 
     * @return boolean
     */
    private function isNotify($model)
    {
        $isnotify = true;

        if (!empty($this->conditions))
        {
            foreach($this->conditions as $key => $value)
            {
                $isnotify = $isnotify && ($model->$key == $value);
            }
        }
        return $isnotify;
    }

    /**
     * @param $modelClassName
     * @param $channel
     * @param $modelData
     * @return bool
     */
    private function saveNotificationSendEmail($modelClassName, $channel, $modelId){
        if(\Yii::$app->request->post('saveNotificationSendEmail') && \Yii::$app->request->post('saveNotificationSendEmail') == 1) {
            if ($channel == NotificationChannels::CHANNEL_MAIL) {
                $notificationSendEmail = NotificationSendEmail::find()->andWhere([
                    'content_id' => $modelId,
                    'classname' => $modelClassName
                ])->one();
                if (is_null($notificationSendEmail)) {
                    $notificationSendEmail = new NotificationSendEmail();
                    $notificationSendEmail->content_id = $modelId;
                    $notificationSendEmail->classname = $modelClassName;
                }

                $ok = $notificationSendEmail->save(false);
                return $ok;
            }
        }
        return true;
    }
}
