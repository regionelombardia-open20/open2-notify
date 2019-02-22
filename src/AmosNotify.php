<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notify
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager;

/**
 *
 * Plugin per la gestione delle notifiche.
 *
 * Si basa su una Behavior (NotifyBehavior) che aggiunge funzionalità al modello con gli Events:
 *
 *      ActiveRecord::EVENT_AFTER_INSERT
 *      ActiveRecord::EVENT_AFTER_UPDATE
 *
 * quando un record del modello viene inserito o aggiornato, viene aggiunto un record nella coda delle notifica (Notification)
 * per ogni canale configurato nella Behavior (default CHANNEL_ALL).
 *
 *      ActiveRecord::EVENT_AFTER_FIND
 * ogni volta che viene chiamato questo evento per il modello la Behavior inserisce o aggiorna un record per il canale CHANNEL_READ
 * nella coda dei letti (NotificationsRead)
 *
 *      CrudController::AFTER_FINDMODEL_EVENT (evento scatenato dal metodo findModel del CrudController)
 * ogni volta che viene chiamato questo evento per il modello la Behavior inserisce o aggiorna un record per il canale CHANNEL_READ_DETAIL
 * nella coda dei letti (NotificationsRead)
 *
 * Canali di notifica disponibili, possono essere configurati tramite il parametro 'channels' della NotifyBehavior es:
 *
 *      'channels' => [NotificationChannels::CHANNEL_MAIL,NotificationChannels::CHANNEL_IMMEDIATE_MAIL ,NotificationChannels::CHANNEL_ALL]
 *
 *      se presente il valore NotificationChannels::CHANNEL_ALL ha la precedenza inserendo tutti i canali disponibili.
 *
 * CHANNEL_MAIL             -- Canale per la gestione notifiche per mail (TODO)
 * CHANNEL_IMMEDIATE_MAIL   -- Canale per la gestione notifiche per mail immediata (TODO)
 * CHANNEL_UI               -- Canale per la gestione notifiche da User Interface (TODO)
 * CHANNEL_SMS              -- Canale per la gestione notifiche per SMS (TODO)
 * CHANNEL_READ             -- Canale per la gestione notifiche da ActiveRecord::EVENT_AFTER_FIND
 * CHANNEL_READ_DETAIL      -- Canale per la gestione notifiche da CrudController::AFTER_FINDMODEL_EVENT
 * CHANNEL_FAVOURITES       -- Canale per la gestione dei preferiti
 * CHANNEL_ALL              -- Tutti i Canali
 */

use lispa\amos\core\module\AmosModule;
use lispa\amos\core\record\Record;
use lispa\amos\notificationmanager\base\NotifierRepository;
use lispa\amos\notificationmanager\base\NotifyWidget;
use lispa\amos\notificationmanager\listeners\NotifyWorkflowListener;
use lispa\amos\notificationmanager\models\NotificationChannels;
use lispa\amos\notificationmanager\utility\NotifyUtility;
use lispa\amos\notificationmanager\models\NotificationsConfOpt;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use Yii;
use yii\base\Event;
use yii\db\ActiveQuery;

/**
 * Class AmosNotify
 * @package lispa\amos\notificationmanager
 */
class AmosNotify extends AmosModule implements \yii\base\BootstrapInterface, NotifyWidget
{
    public $batchFromDate; // format 'yyyy-mm-dd'
    
    public $defaultSchedule = NotificationsConfOpt::EMAIL_DAY;

    public $confirmEmailNotification = false;

    private static $notifyworkflowlistener;

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'lispa\amos\notificationmanager\controllers';

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        self::$notifyworkflowlistener = new NotifyWorkflowListener();
        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return "notify";
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::setAlias('@lispa/amos/notificationmanager/commands', __DIR__ . '/commands/');
        // initialize the module with the configuration loaded from config.php
        \Yii::configure($this, require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php'));
    }

    /**
     * @param \yii\console\Application $app
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'lispa\amos\notificationmanager\commands';
        } else {
            Event::on(Record::className(), SimpleWorkflowBehavior::EVENT_AFTER_CHANGE_STATUS, [self::$notifyworkflowlistener, 'afterChangeStatus']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [
            'Notification' => __NAMESPACE__ . '\\' . 'models\Notification',
            'NotificationConf' => __NAMESPACE__ . '\\' . 'models\NotificationConf',
            'NotificationsRead' => __NAMESPACE__ . '\\' . 'models\NotificationsRead',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [];
    }

    /**
     * @param int $uid
     * @param string $class_name
     * @param ActiveQuery|null $externalquery
     * @param NotificationChannels $channel
     */
    public function notificationOff($uid, $class_name, $externalquery = null, $channel)
    {
        try {
            $repository = new NotifierRepository();
            $repository->notificationOff($uid, $class_name, $externalquery, $channel);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param int $uid
     * @param string $class_name
     * @param ActiveQuery|null $externalquery
     * @param NotificationChannels $channel
     */
    public function notificationOn($uid, $class_name, $externalquery = null, $channel)
    {
        try {
            $repository = new NotifierRepository();
            $repository->notificationOn($uid, $class_name, $externalquery, $channel);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param int $uid
     * @param string $class_name
     * @param ActiveQuery|null $externalquery
     * @return false|int|null|string
     */
    public function countNotRead($uid, $class_name, $externalquery = null)
    {
        $result = 0;
        try {
            $repository = new NotifierRepository();
            $result = $repository->countNotRead($uid, $class_name, $externalquery);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * @param $model
     * @param int|null $uid
     * @return bool
     */
    public function modelIsRead($model, $uid = null)
    {
        $result = false;
        try {
            $repository = new NotifierRepository();
            $result = $repository->modelIsRead($model, $uid);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * @param string $modelClassName
     * @param string $type
     * @return array
     */
    public static function manageNewChannelNotifications($modelClassName, $channel, $type)
    {
        $retval = false;
        try {
            $notificationChannel = new NotificationChannels();
            $retval = $notificationChannel->manageNewChannelNotifications($modelClassName, $channel, $type);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $retval;
    }

    /**
     * @param int $uid
     * @param string $class_name
     * @param int $contentId
     * @return bool
     */
    public function favouriteOn($uid, $class_name, $contentId)
    {
        $ok = true;
        try {
            $repository = new NotifierRepository();
            $ok = $repository->favouriteOn($uid, $class_name, $contentId);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $ok;
    }

    /**
     * @param int $uid
     * @param string $class_name
     * @param int $contentId
     * @return bool
     */
    public function favouriteOff($uid, $class_name, $contentId)
    {
        $ok = true;
        try {
            $repository = new NotifierRepository();
            $ok = $repository->favouriteOff($uid, $class_name, $contentId);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $ok;
    }

    /**
     * @param $model
     * @param int|null $uid
     * @return bool
     */
    public function isFavorite($model, $uid = null)
    {
        $result = false;
        try {
            $repository = new NotifierRepository();
            $result = $repository->isFavorite($model, $uid);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * The method save the notification configuration.
     * @param int $userId
     * @param int $emailFrequency
     * @param int $smsFrequency
     * @return bool
     */
    public function saveNotificationConf($userId, $emailFrequency = 0, $smsFrequency = 0)
    {
        $notifyUtility = new NotifyUtility();
        return $notifyUtility->saveNotificationConf($userId, $emailFrequency, $smsFrequency);
    }
}
