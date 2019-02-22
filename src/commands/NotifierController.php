<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\commands
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\commands;

use lispa\amos\admin\AmosAdmin;
use lispa\amos\admin\base\ConfigurationManager;
use lispa\amos\admin\models\UserProfile;
use lispa\amos\community\models\Community;
use lispa\amos\core\user\User;
use lispa\amos\cwh\AmosCwh;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\base\builder\AMailBuilder;
use lispa\amos\notificationmanager\base\BuilderFactory;
use lispa\amos\notificationmanager\models\base\NotificationSendEmail;
use lispa\amos\notificationmanager\models\Notification;
use lispa\amos\notificationmanager\models\NotificationChannels;
use lispa\amos\notificationmanager\models\NotificationConf;
use lispa\amos\notificationmanager\models\NotificationsConfOpt;
use lispa\amos\notificationmanager\models\NotificationsRead;
use Exception;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * Class NotifierController
 * @package lispa\amos\notificationmanager\commands
 */
class NotifierController extends Controller
{
    public $weekMails = false;
    public $dayMails = false;
    public $monthMails = false;
    public $immediateMails = false;

    /**
     * @param string $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        return ['weekMails', 'dayMails', 'monthMails', 'immediateMails'];
    }

    /**
     * This action sends nightly mails.
     */
    public function actionMailChannel()
    {
        try {
            $type = $this->evaluateOperations();
            Console::stdout('Begin mail-channel ' . $type . PHP_EOL);
            $users = $this->loadUser($type);
            $factory = new BuilderFactory();
            if ($type == NotificationsConfOpt::EMAIL_IMMEDIATE) {
                Console::stdout('BUILD ' . $type . PHP_EOL);
                $builder = $factory->create(BuilderFactory::CONTENT_IMMEDIATE_MAIL_BUILDER);
            } else {
                $builder = $factory->create(BuilderFactory::CONTENT_MAIL_BUILDER);
            }
            /** @var AmosCwh $cwhModule */
            $cwhModule = Yii::$app->getModule('cwh');
            $this->notifyUserArray($cwhModule, $users, $builder);
            Console::stdout('End mail-channel ' . $type . PHP_EOL);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     *
     */
    public function actionSMSChannel()
    {
        try {

        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param int $notify_id
     * @param int $reader_id
     */
    protected function notifyReadFlag($notify_id, $reader_id)
    {
        try {
            $model = new NotificationsRead();
            $model->notification_id = $notify_id;
            $model->user_id = $reader_id;
            $model->save();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $schedule
     * @return array|null
     */
    protected function loadUser($schedule)
    {
        $result = null;
        try {
            $module = AmosNotify::getInstance();
            /** @var AmosAdmin $adminModule */
            $adminModule = Yii::$app->getModule('admin');

            $query = new Query();
            $query->from(UserProfile::tableName());
            $query->innerJoin(User::tableName(),
                UserProfile::tableName() . '.user_id = ' . User::tableName() . '.id');
            $query->leftJoin(NotificationConf::tableName(),
                NotificationConf::tableName() . '.user_id = ' . UserProfile::tableName() . '.user_id');
            $query->where([UserProfile::tableName() . '.deleted_at' => null]);
            $query->andWhere([UserProfile::tableName() . '.attivo' => UserProfile::STATUS_ACTIVE]);
            $query->andWhere([User::tableName() . '.status' => User::STATUS_ACTIVE]);
            if (
                $adminModule->confManager->isVisibleBox('box_privacy', ConfigurationManager::VIEW_TYPE_FORM) &&
                $adminModule->confManager->isVisibleField('privacy', ConfigurationManager::VIEW_TYPE_FORM)
            ) {
                $query->andWhere([UserProfile::tableName() . '.privacy' => 1]);
            }

            if ($schedule == $module->defaultSchedule) {
                $query->andWhere(['or',
                    [NotificationConf::tableName() . '.email' => $schedule],
                    [NotificationConf::tableName() . '.email' => null]
                ]);
            } else {
                $query->andWhere([NotificationConf::tableName() . '.email' => $schedule]);
            }
            $query->orderBy([UserProfile::tableName() . '.user_id' => SORT_ASC]);
            $query->select(UserProfile::tableName() . '.*');
            $result = $query->all();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * @return int
     */
    private function evaluateOperations()
    {
        $retValue = NotificationsConfOpt::EMAIL_DAY;

        if ($this->dayMails) {
            $retValue = NotificationsConfOpt::EMAIL_DAY;
        } elseif ($this->weekMails) {
            $retValue = NotificationsConfOpt::EMAIL_WEEK;
        } elseif ($this->monthMails) {
            $retValue = NotificationsConfOpt::EMAIL_MONTH;
        } elseif ($this->immediateMails) {
            $retValue = NotificationsConfOpt::EMAIL_IMMEDIATE;
        }

        return $retValue;
    }

    /**
     *
     */
    public function actionScopeMailChannel()
    {
        $comminities = [2634];

        foreach ($comminities as $comminity_id) {
            Console::stdout('Start scope-mail-channel for community:' . $comminity_id . PHP_EOL);
            $community = Community::findOne(['id' => $comminity_id]);
            if (!is_null($community)) {
                $users = $community->getCommunityUserMms()->select('user_profile.user_id, user_profile.created_at')->all();
                /** @var AmosCwh $cwhModule */
                $cwhModule = Yii::$app->getModule('cwh');
                $cwhModule->scope = ['community' => $comminity_id];
                $factory = new BuilderFactory();
                $builder = $factory->create(BuilderFactory::CONTENT_MAIL_BUILDER);
                $this->notifyUserArray($cwhModule, $users, $builder);
            }
            Console::stdout('End scope-mail-channel for community:' . $comminity_id . PHP_EOL);
        }
        $cwhModule->scope = null;
    }

    /**
     * @param AmosCwh|null $cwhModule
     * @param array $users
     * @param AMailBuilder $builder
     * @throws \yii\base\InvalidConfigException
     */
    private function notifyUserArray($cwhModule, $users, $builder)
    {
        foreach ($users as $user) {
            $uid = $user['user_id'];
            Console::stdout('Start working on user ' . $uid . PHP_EOL);
            $query = Notification::find()
                ->innerJoin(NotificationSendEmail::tableName(),'notification.class_name = notification_send_email.classname AND notification.content_id = notification_send_email.content_id')
                ->leftJoin(NotificationsRead::tableName(), ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName() . '.notification_id'), NotificationsRead::tableName() . '.user_id' => $uid])
                ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
                ->andWhere([NotificationsRead::tableName() . '.user_id' => null])
                ->andWhere(['>=', Notification::tableName() . ".created_at", new \yii\db\Expression("UNIX_TIMESTAMP('" . $user['created_at'] . "')")]);

            $notify = AmosNotify::instance();
            if (isset($notify->batchFromDate)) {
                $query->andWhere(['>=', Notification::tableName() . '.created_at', strtotime($notify->batchFromDate)]);
            }

            if (isset($cwhModule)) {
                $modelsEnabled = \lispa\amos\cwh\models\CwhConfigContents::find()->addSelect('classname')->column();
                $andWhere = "";
                $i = 0;
                foreach ($modelsEnabled as $classname) {
                    $cwhActiveQuery = new \lispa\amos\cwh\query\CwhActiveQuery($classname, [
                        'queryBase' => $classname::find(),
                        'userId' => $uid
                    ]);
                    $queryModel = $cwhActiveQuery->getQueryCwhOwnInterest();
                    $modelIds = $queryModel->select($classname::tableName() . '.id')->column();
                    if (!empty($modelIds)) {
                        if ($i != 0) {
                            $andWhere .= " OR ";
                        }
                        $andWhere .= '(' . Notification::tableName() . ".class_name = '" . addslashes($classname) . "' AND " . Notification::tableName() . '.content_id in (' . implode(',', $modelIds) . '))';
                        $i++;
                    }
                    unset($cwhActiveQuery);
                }
                if (!empty($andWhere)) {
                    $query->andWhere($andWhere);
                } else {
                    Console::stdout('End working on user without interest' . $uid . PHP_EOL);
                    continue;
                }
            }
            $query->orderBy('class_name');
            $result = $query->all();
            if (!empty($result)) {
                $builder->sendEmail([$uid], $result);
            }
            /** @var Notification $notify */
            foreach ($result as $notify) {
                $this->notifyReadFlag($notify->id, $uid);
            }
            Console::stdout('End working on user ' . $uid . PHP_EOL);
            unset($query);
        }
    }
}
