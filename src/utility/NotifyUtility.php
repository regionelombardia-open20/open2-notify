<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\utility
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\utility;

use lispa\amos\notificationmanager\models\NotificationConf;
use lispa\amos\notificationmanager\models\NotificationsConfOpt;
use yii\base\Object;

/**
 * Class NotifyUtility
 * @package lispa\amos\notificationmanager\utility
 */
class NotifyUtility extends Object
{
    /**
     * The method save the notification configuration.
     * @param int $userId
     * @param int $emailFrequency
     * @param int $smsFrequency
     * @return bool
     */
    public function saveNotificationConf($userId, $emailFrequency = 0, $smsFrequency = 0)
    {
        // Check the params type
        if (!is_numeric($userId) || !is_numeric($emailFrequency) || !is_numeric($smsFrequency)) {
            return false;
        }
        // Check the params presence
        if (!$emailFrequency && !$smsFrequency) {
            return false;
        }
        // Find the notification conf for the user
        $notificationConf = NotificationConf::findOne(['user_id' => $userId]);
        if (is_null($notificationConf)) {
            $notificationConf = new NotificationConf();
            $notificationConf->user_id = $userId;
        }
        if ($emailFrequency) {
            // Check the params correct value for email frequency
            $emailFrequencyValues = NotificationsConfOpt::emailFrequencyValues();
            if (!in_array($emailFrequency, $emailFrequencyValues)) {
                return false;
            }
            $notificationConf->email = $emailFrequency;
        }
        if ($smsFrequency) {
            // Check the params correct value for sms frequency
            $smsFrequencyValues = NotificationsConfOpt::smsFrequencyValues();
            if (!in_array($smsFrequency, $smsFrequencyValues)) {
                return false;
            }
            $notificationConf->sms = $smsFrequency;
        }
        $ok = $notificationConf->save();
        return $ok;
    }
}
