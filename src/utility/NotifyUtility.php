<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notificationmanager\utility
 * @category   CategoryName
 */

namespace open20\amos\notificationmanager\utility;

use open20\amos\core\models\ModelsClassname;
use open20\amos\core\user\User;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\models\NotificationConf;
use open20\amos\notificationmanager\models\NotificationsConfOpt;
use open20\amos\notificationmanager\models\NotificationconfNetwork;
use yii\base\BaseObject;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * Class NotifyUtility
 * @package open20\amos\notificationmanager\utility
 */
class NotifyUtility extends BaseObject
{
    /**
     * @var AmosNotify $notifyModule
     */
    protected $notifyModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->notifyModule = AmosNotify::instance();
    }

    /**
     * The method save the notification configuration.
     * @param int $userId
     * @param int $emailFrequency
     * @param int $smsFrequency
     * @param array $params
     * @return bool
     */
    public function saveNotificationConf($userId, $emailFrequency = 0, $smsFrequency = 0, $params = [])
    {
        // Check the params type
        if (!is_numeric($userId) || !is_numeric($emailFrequency) || !is_numeric($smsFrequency)) {
            return false;
        }
        // Check the params presence
        if (!$emailFrequency && !$smsFrequency) {
            return false;
        }
        /** @var NotificationConf $notificationConfModel */
        $notificationConfModel = $this->notifyModule->createModel('NotificationConf');
        // Find the notification conf for the user
        $notificationConf = $notificationConfModel::findOne(['user_id' => $userId]);
        if (is_null($notificationConf)) {
            /** @var NotificationConf $notificationConf */
            $notificationConf = $this->notifyModule->createModel('NotificationConf');
            $notificationConf->user_id = $userId;
        }
        if ($emailFrequency) {
            /** @var NotificationsConfOpt $notificationConfOpt */
            $notificationConfOpt = $this->notifyModule->createModel('NotificationsConfOpt');
            // Check the params correct value for email frequency
            $emailFrequencyValues = $notificationConfOpt::emailFrequencyValues();
            if (!in_array($emailFrequency, $emailFrequencyValues)) {
                return false;
            }
            $notificationConf->email = $emailFrequency;
        }
        if ($smsFrequency) {
            /** @var NotificationsConfOpt $notificationConfOpt */
            $notificationConfOpt = $this->notifyModule->createModel('NotificationsConfOpt');
            // Check the params correct value for sms frequency
            $smsFrequencyValues = $notificationConfOpt::smsFrequencyValues();
            if (!in_array($smsFrequency, $smsFrequencyValues)) {
                return false;
            }
            $notificationConf->sms = $smsFrequency;
        }

        if (isset($params['notifications_enabled'])) {
            $notificationConf->notifications_enabled = $params['notifications_enabled'];
        }
        if (isset($params['notify_content_pubblication'])) {
            $notificationConf->notify_content_pubblication = $params['notify_content_pubblication'];
        }
        if (isset($params['notify_comments'])) {
            $notificationConf->notify_comments = $params['notify_comments'];
        }
        $ok = $notificationConf->save();
        $this->saveNetworkNotification($userId, $params);
        return $ok;
    }

    /**
     * @param $userId
     * @param $params
     */
    public function saveNetworkNotification($userId, $params)
    {
        if (!empty($params['notifyCommunity'])) {
            foreach ($params['notifyCommunity'] as $communityId => $value) {
                $modelClassname = ModelsClassname::find()->andWhere(['module' => 'community'])->one();
                if ($modelClassname) {
                    /** @var NotificationconfNetwork $notificationConfNetworkModel */
                    $notificationConfNetworkModel = $this->notifyModule->createModel('NotificationconfNetwork');
                    $confNetwork = $notificationConfNetworkModel::find()
                        ->andWhere(['models_classname_id' => $modelClassname->id, 'record_id' => $communityId])
                        ->andWhere(['user_id' => $userId])->one();
                    if (empty($confNetwork)) {
                        /** @var NotificationconfNetwork $confNetwork */
                        $confNetwork = $this->notifyModule->createModel('NotificationconfNetwork');
                    }
                    $confNetwork->user_id = $userId;
                    $confNetwork->models_classname_id = $modelClassname->id;
                    $confNetwork->record_id = $communityId;
                    $confNetwork->email = $value;
                    $confNetwork->save(false);
                }
            }
        }
    }

    /**
     * @param $userId
     * @param $notificationType
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getNetworkNotificationConf($userId, $notificationType)
    {
        /** @var NotificationconfNetwork $notificationConfNetworkModel */
        $notificationConfNetworkModel = AmosNotify::instance()->createModel('NotificationconfNetwork');
        /** @var  $query ActiveQuery */
        $query = $notificationConfNetworkModel::find()
            ->andWhere(['user_id' => $userId])
            ->andWhere(['IS NOT', 'record_id', null])
            ->andWhere(['IS NOT', 'models_classname_id', null]);

        $query->andWhere(['!=', 'email', $notificationType]);
        $query->andWhere(['IS NOT', 'email', null]);

        return $query->all();
    }

    /**
     * This method set the user default notifications configurations.
     * @param int $userId
     * @return bool
     */
    public function setDefaultNotificationsConfs($userId)
    {
        $emailFrequency = NotificationsConfOpt::EMAIL_DAY;
        $smsFrequency = 0;
        $params = [
            'notifications_enabled' => 1,
            'notify_content_pubblication' => 1,
            'notify_comments' => 1,
        ];
        return $this->saveNotificationConf($userId, $emailFrequency, $smsFrequency, $params);
    }

    /**
     * This method add the notifications configurations for all users that these configurations are missing.
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function addMissingUserNotificationsConfs()
    {
        /** @var ActiveQuery $query */
        $query = User::find();
        $query->select(['id']);
        $allUserIds = $query->column();

        /** @var NotificationConf $notificationConfModel */
        $notificationConfModel = $this->notifyModule->createModel('NotificationConf');

        /** @var ActiveQuery $queryNotify */
        $queryNotify = $notificationConfModel::find();
        $queryNotify->select(['user_id']);
        $allNotificationConfsUserIds = $queryNotify->column();

        $missingNotificationConfsUserIds = array_diff($allUserIds, $allNotificationConfsUserIds);

        $allOk = true;
        foreach ($missingNotificationConfsUserIds as $userId) {
            $ok = $this->setDefaultNotificationsConfs($userId);
            if (!$ok) {
                $allOk = false;
            }
        }

        return $allOk;
    }
}
