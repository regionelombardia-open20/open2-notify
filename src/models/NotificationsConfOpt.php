<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\models
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

use open20\amos\notificationmanager\AmosNotify;
use yii\base\BaseObject;

/**
 * Class NotificationsConfOpt
 * @package open20\amos\notificationmanager\models
 */
class NotificationsConfOpt extends BaseObject
{
    const EMAIL_OFF = 0xFFFF;
    const EMAIL_IMMEDIATE = 0x0001;
    const EMAIL_DAY = 0x0002;
    const EMAIL_WEEK = 0x0004;
    const EMAIL_MONTH = 0x0008;
    
    const SMS_IMMEDIATE = NotificationsConfOpt::EMAIL_IMMEDIATE;
    const SMS_DAY = NotificationsConfOpt::EMAIL_DAY;
    const SMS_WEEK = NotificationsConfOpt::EMAIL_WEEK;
    const SMS_MONTH = NotificationsConfOpt::EMAIL_MONTH;
    
    const NEWSLETTER = 0x0010;
    
    /**
     * This method return the email frequencies.
     * @return int[]
     */
    public static function emailFrequencyValues()
    {
        return [
            NotificationsConfOpt::EMAIL_OFF,
            NotificationsConfOpt::EMAIL_IMMEDIATE,
            NotificationsConfOpt::EMAIL_DAY,
            NotificationsConfOpt::EMAIL_WEEK,
            NotificationsConfOpt::EMAIL_MONTH
        ];
    }
    
    /**
     * This method return the email frequency values and labels.
     * @return array
     */
    public static function emailFrequencyValueAndLabels($alsoImmediate = true)
    {
        if ($alsoImmediate){
            return [
                NotificationsConfOpt::EMAIL_OFF => AmosNotify::t('amosnotify', '#never'),
                NotificationsConfOpt::EMAIL_IMMEDIATE => AmosNotify::t('amosnotify', 'Immediate emails'),
                NotificationsConfOpt::EMAIL_DAY => AmosNotify::t('amosnotify', 'Daily emails'),
                NotificationsConfOpt::EMAIL_WEEK => AmosNotify::t('amosnotify', 'Weekly emails'),
                NotificationsConfOpt::EMAIL_MONTH => AmosNotify::t('amosnotify', 'Monthly emails')
            ];        
        } else {
            return [
                NotificationsConfOpt::EMAIL_OFF => AmosNotify::t('amosnotify', '#never'),
                NotificationsConfOpt::EMAIL_DAY => AmosNotify::t('amosnotify', 'Daily emails'),
                NotificationsConfOpt::EMAIL_WEEK => AmosNotify::t('amosnotify', 'Weekly emails'),
                NotificationsConfOpt::EMAIL_MONTH => AmosNotify::t('amosnotify', 'Monthly emails')
            ];        
        }
    }
    
    /**
     * This method return the email frequency label.
     * @param int $frequency
     * @return string
     */
    public static function emailFrequencyLabel($frequency)
    {
        $labelFrequency = [
            NotificationsConfOpt::EMAIL_OFF => AmosNotify::t('amosnotify', 'Never'),
            NotificationsConfOpt::EMAIL_IMMEDIATE => AmosNotify::t('amosnotify', 'Immediate emails'),
            NotificationsConfOpt::EMAIL_DAY => AmosNotify::t('amosnotify', 'Daily emails'),
            NotificationsConfOpt::EMAIL_WEEK => AmosNotify::t('amosnotify', 'Weekly emails'),
            NotificationsConfOpt::EMAIL_MONTH => AmosNotify::t('amosnotify', 'Monthly emails')
        ];
        return $labelFrequency[$frequency];
    }
    
    /**
     * This method return the email frequency values and labels.
     * @return array
     */
    public static function smsFrequencyValueAndLabels()
    {
        return [
            NotificationsConfOpt::SMS_IMMEDIATE => AmosNotify::t('amosnotify', 'Immediate sms'),
            NotificationsConfOpt::SMS_DAY => AmosNotify::t('amosnotify', 'Daily sms'),
            NotificationsConfOpt::SMS_WEEK => AmosNotify::t('amosnotify', 'Weekly sms'),
            NotificationsConfOpt::SMS_MONTH => AmosNotify::t('amosnotify', 'Monthly sms')
        ];
    }
    
    /**
     * This method return the sms frequencies.
     * @return int[]
     */
    public static function smsFrequencyValues()
    {
        return [
            NotificationsConfOpt::SMS_IMMEDIATE,
            NotificationsConfOpt::SMS_DAY,
            NotificationsConfOpt::SMS_WEEK,
            NotificationsConfOpt::SMS_MONTH
        ];
    }
    
    /**
     * This method return the sms frequency label.
     * @param int $frequency
     * @return string
     */
    public static function smsFrequencyLabel($frequency)
    {
        $labelFrequency = [
            NotificationsConfOpt::SMS_IMMEDIATE => AmosNotify::t('amosnotify', 'Immediate sms'),
            NotificationsConfOpt::SMS_DAY => AmosNotify::t('amosnotify', 'Daily sms'),
            NotificationsConfOpt::SMS_WEEK => AmosNotify::t('amosnotify', 'Weekly sms'),
            NotificationsConfOpt::SMS_MONTH => AmosNotify::t('amosnotify', 'Monthly sms')
        ];
        return $labelFrequency[$frequency];
    }
}
