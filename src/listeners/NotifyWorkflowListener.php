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

namespace open20\amos\notificationmanager\listeners;

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    piattaforma-openinnovation
 * @category   CategoryName
 */

use open20\amos\core\record\Record;
use open20\amos\core\user\User;
use open20\amos\notificationmanager\base\BuilderFactory;
use open20\amos\notificationmanager\models\ChangeStatusEmail;
use open20\amos\notificationmanager\models\NotificationConf;
use open20\amos\notificationmanager\record\NotifyRecordInterface;
use ReflectionClass;
use Yii;
use yii\base\Exception;


class NotifyWorkflowListener extends \yii\base\BaseObject
{

    /**
     * @param $event
     * @return bool
     */
    public function afterChangeStatus($event)
    {
        try {
            /** @var Record $owner */
            $owner = $event->sender->owner;
            if (!empty($owner)) {
                if ($owner instanceof NotifyRecordInterface) {
                    if ($owner->sendNotification()) {
                        if (empty($owner->mailStatuses)) {
                            $this->sendValidatorsEmail($event, $owner);
                        } else {
                            $this->setCustomEmails($event, $owner);
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }
        return true;
    }


    /**
     * Status to Validate, send mail to model validators
     * @param $event
     * @param $model
     */
    public function sendValidatorsEmail($event, $model)
    {
        try {
            $module = \Yii::$app->getModule('notify');
            $rc = new ReflectionClass($model->className());
            if ($rc->hasMethod('getToValidateStatus') && $rc->hasMethod('getValidatedStatus')) {

                if (!strcmp($model->getToValidateStatus(), $event->getEndStatus()->getId())) {
                    $factory = new BuilderFactory();
                    $builder = $factory->create(BuilderFactory::VALIDATORS_MAIL_BUILDER);
                    if ($model instanceof Record) {
                        //send email to VALIDATORS
                        $userIds = $model->getValidatorUsersId();

                        // send email to FACILITATOR and FACILITATOR_EXTERNAL
                        $user = User::findOne($model->created_by);
                        if (!is_null($user)) {
                            /**var $userprofile UserProfile */
                            $userprofile = $user->getUserProfile()->one();
                            if (!is_null($userprofile)) {
                                $facilUserProfile = $userprofile->getFacilitatorOrDefFacilitator();
                                if (!is_null($facilUserProfile)) {
                                    $userIds[] = $facilUserProfile->user_id;
                                }
                                $facilExternalUserProfile = $userprofile->externalFacilitator;
                                if (!is_null($facilUserProfile)) {
                                    $userIds[] = $facilExternalUserProfile->user_id;
                                }

                            }
                        }
                        $userIds = array_unique($userIds);
                        $builder->sendEmail($userIds, [$model]);
                    }
                } else {
                    if (!strcmp($model->getValidatedStatus(), $event->getEndStatus()->getId())) {
                        $sentPersonalized = self::sendPersonalizedValidatedEmail($model, $module);
                        if (!$sentPersonalized) {
                            $validator_id = \Yii::$app->user->id;//$model->getStatusLastUpdateUser($model->getValidatedStatus());
                            $factory = new BuilderFactory();
                            $to[] = $validator_id;
                            if ($model->getNotifiedUserId() != $validator_id) {
                                $to[] = $model->getNotifiedUserId();
                            }
                            $builder = $factory->create(BuilderFactory::VALIDATED_MAIL_BUILDER);
                            $to = array_unique($to);
                            $builder->sendEmail($to, [$model]);
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }

    }

    /**
     * @param $model
     * @param $module
     * @return bool
     * Send personalized email after Validate a model
     */
    public static function sendPersonalizedValidatedEmail($model, $module)
    {

        if (!empty($module->personalizedValidatedEmail)) {
            if (!empty($module->personalizedValidatedEmail[get_class($model)])) {
                $personalizedClass = $module->personalizedValidatedEmail[get_class($model)]['class'];
                $method = $module->personalizedValidatedEmail[get_class($model)]['method'];
                $personalizedClass::$method($model);
                return true;
            }
        }
        return false;
    }


    /**
     * Send customized mails on status changes defined in model->mailStatuses
     * @param $event
     * @param $model
     */
    public function setCustomEmails($event, $model)
    {
        try {
            $eventEndStatus = $event->getEndStatus()->getId();
            //if model was new record the start status is not defined, check initial status id
            if (!is_null($event->getStartStatus())) {
                $eventStartStatus = $event->getStartStatus()->getId();
            } else {
                $eventStartStatus = $model->getWorkflowSource()->getWorkflow($model->formName() . 'Workflow')->getInitialStatusId();
            }

            /**
             * @var string $endStatus
             * @var ChangeStatusEmail $email
             */
            foreach ($model->mailStatuses as $endStatus => $email) {
                // if the model is going to the status for which mail sending is needed
                if (!strcmp($endStatus, $eventEndStatus)) {
                    //if send mail independently by start status or the start status matches
                    $startStatus = $email->startStatus;
                    if (!isset($startStatus) || !strcmp($startStatus, $eventStartStatus)) {
                        $factory = new BuilderFactory();
                        $builder = $factory->create(BuilderFactory::CUSTOM_MAIL_BUILDER, $email, $endStatus);

                        if (!empty($email->recipientUserIds)) {
                            $builder->sendEmail([$email->recipientUserIds], [$model]);
                        } elseif ($email->toCreator) {
                            $recipients[] = $model->getNotifiedUserId();
                            if ($email->toValidator) {
                                $validator_id = $model->getStatusLastUpdateUser($endStatus);
                                $recipients[] = $validator_id;
                            }
                            $builder->sendEmail($recipients, [$model]);
                        } else { //mail to validators
                            $userIds = $model->getValidatorUsersId();
                            if (empty($userIds)) {
                                $user = User::findOne($model->created_by);
                                if (!is_null($user)) {
                                    /**var $userprofile UserProfile */
                                    $userprofile = $user->getUserProfile()->one();
                                    if (!is_null($userprofile)) {
                                        $facilUserProfile = $userprofile->getFacilitatorOrDefFacilitator();
                                        if (!is_null($facilUserProfile)) {
                                            $userIds[] = $facilUserProfile->user_id;
                                        }
                                    }
                                }
                            }
                            $builder->sendEmail($userIds, [$model]);
                        }
                    }
                }
            }

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

}