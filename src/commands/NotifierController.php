<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notificationmanager\commands
 * @category   CategoryName
 */
namespace open20\amos\notificationmanager\commands;

use open20\amos\admin\AmosAdmin;
use open20\amos\admin\base\ConfigurationManager;
use open20\amos\admin\models\UserProfile;
use open20\amos\community\models\Community;
use open20\amos\core\user\User;
use open20\amos\cwh\AmosCwh;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\base\builder\AMailBuilder;
use open20\amos\notificationmanager\base\BuilderFactory;
use open20\amos\notificationmanager\models\base\NotificationSendEmail;
use open20\amos\notificationmanager\models\Notification;
use open20\amos\notificationmanager\models\NotificationChannels;
use open20\amos\notificationmanager\models\NotificationConf;
use open20\amos\notificationmanager\models\NotificationPersonalizedQueryInterface;
use open20\amos\notificationmanager\models\NotificationsConfOpt;
use open20\amos\notificationmanager\models\NotificationsRead;
use open20\amos\notificationmanager\utility\NotifyUtility;
use Exception;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

/**
 * Class NotifierController
 * @package open20\amos\notificationmanager\commands
 */
class NotifierController extends Controller
{

    public $weekMails = false;
    public $dayMails = false;
    public $monthMails = false;
    public $immediateMails = false;

    /**
     * @var AmosNotify $notifyModule
     */
    public $notifyModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->notifyModule = AmosNotify::instance();
    }

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
            $this->notifyUserArray($cwhModule, $users, $builder, $type);
            Console::stdout('End mail-channel ' . $type . PHP_EOL);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     *
     */
    public function actionSMSChannel()
    {
        try {
            
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param int $notify_id
     * @param int $reader_id
     */
    protected function notifyReadFlag($notify_id, $reader_id)
    {
        try {
            /** @var NotificationsRead $model */
            $model = $this->notifyModule->createModel('NotificationsRead');
            $model->notification_id = $notify_id;
            $model->user_id = $reader_id;
            $model->save();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
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
            $query->andWhere(['OR',
                [NotificationConf::tableName() . '.notifications_enabled' => 1],
                [NotificationConf::tableName() . '.notifications_enabled' => NULL],
            ]);
            $query->andwhere([UserProfile::tableName() . '.deleted_at' => null]);
            $query->andWhere([UserProfile::tableName() . '.attivo' => UserProfile::STATUS_ACTIVE]);
            $query->andWhere([User::tableName() . '.status' => User::STATUS_ACTIVE]);
            if (
                $adminModule->confManager->isVisibleBox('box_privacy', ConfigurationManager::VIEW_TYPE_FORM) &&
                $adminModule->confManager->isVisibleField('privacy', ConfigurationManager::VIEW_TYPE_FORM)
            ) {
                $query->andWhere([UserProfile::tableName() . '.privacy' => 1]);
            }

            // clone the query withou the filter for type o cron
            $queryConfCommunity = clone $query;

            // filter the query for type of cron
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

            // query for network notificatiomn
            if(!empty($schedule)) {
                $queryConfCommunity
                    ->orderBy([UserProfile::tableName() . '.user_id' => SORT_ASC. ', notificationconf_network.*'])
                    ->select(UserProfile::tableName() . '.*')
                    ->innerJoin('notificationconf_network', 'notificationconf_network.user_id = user_profile.user_id')
                    ->andWhere(['IS NOT', 'record_id', null])
                    ->andWhere(['IS NOT', 'models_classname_id', null])
                    ->andWhere(['notificationconf_network.email' => $schedule]);
            }

            $query->union($queryConfCommunity);
            //Console::stdout($query->createCommand()->rawSql . PHP_EOL);

            $result = $query->all();
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), Logger::LEVEL_ERROR);
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
                $users = $community->getCommunityUserMms()->select('user_profile.user_id, user_profile.notify_from_editorial_staff, user_profile.created_at')->asArray()->all();
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
    private function notifyUserArray($cwhModule, $users, $builder, $type = null)
    {
        $connection = \Yii::$app->db;
        $transaction = null;
        try {
            foreach ($users as $user) {
                $transaction = $connection->beginTransaction();
                $uid = $user['user_id'];
                /** @var NotificationConf $notificationConfModel */
                $notificationConfModel = $this->notifyModule->createModel('NotificationConf');
                /** @var  $notificationconf NotificationConf*/
                $notificationconf = $notificationConfModel::find()->andWhere(['user_id' => $uid])->one();
                Console::stdout('Start working on user ' . $uid . PHP_EOL);
                $notify_editorial_staff = $user['notify_from_editorial_staff'];

                if (!empty($notificationconf)) {
                    /** @var Notification $notificationModel */
                    $notificationModel = $this->notifyModule->createModel('Notification');
                    $query = $notificationModel::find()
                        ->leftJoin(NotificationsRead::tableName(), ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName() . '.notification_id'), NotificationsRead::tableName() . '.user_id' => $uid])
                        ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
                        ->andWhere([NotificationsRead::tableName() . '.user_id' => null])
                        ->andWhere(['>=', Notification::tableName() . ".created_at", new \yii\db\Expression("UNIX_TIMESTAMP('" . $user['created_at'] . "')")]);

                    // Get the netowrks to not notify
                    $notificationNetworkConfDontNotify = NotifyUtility::getNetworkNotificationConf($uid, $type);
                    $networkConfArray = [];
                    foreach ($notificationNetworkConfDontNotify as $networkConf) {
                        $networkConfArray[$networkConf->models_classname_id] = $networkConf->record_id;
                    }
                    if(!empty($networkConfArray)) {
                        foreach ($networkConfArray as $classname_id => $record_id) {

                            if(!empty($classname_id) && !empty($record_id)) {
                                $query->andWhere([
                                    'AND',
                                    ['!=', 'models_classname_id', $classname_id],
                                    ['!=', 'record_id', $record_id]
                                ]);
                            }
                        }
                    }


                    if ($this->notifyModule->confirmEmailNotification) {
                        $query->innerJoin(NotificationSendEmail::tableName(), 'notification.class_name = notification_send_email.classname AND notification.content_id = notification_send_email.content_id');
                    }


                    if (isset($this->notifyModule->batchFromDate)) {
                        $query->andWhere(['>=', Notification::tableName() . '.created_at', strtotime($this->notifyModule->batchFromDate)]);
                    }

                    if (isset($cwhModule)) {
                        $modelsEnabled = \open20\amos\cwh\models\CwhConfigContents::find()->addSelect('classname')->column();
                        $andWhere = "";
                        $i = 0;
                        foreach ($modelsEnabled as $classname) {
                            $cwhActiveQuery = new \open20\amos\cwh\query\CwhActiveQuery($classname, [
                                'queryBase' => $classname::find(),
                                'userId' => $uid
                            ]);
                            $cwhActiveQuery::$userProfile = null; //reset user profile

                            /** if exist table news and module disable sending notification to certain types of news */
                            $tableNews = Yii::$app->db->schema->getTableSchema('news');
                            $tableCategoryNews = Yii::$app->db->schema->getTableSchema('news_categorie');
                            if (class_exists($classname) && $tableNews && $tableCategoryNews && isset($tableCategoryNews->columns['notify_category'])) {
                                if ($classname == \open20\amos\news\models\News::className()) {
                                    /** @var Notification $notificationModel */
                                    $notificationModel = $this->notifyModule->createModel('Notification');
                                    $newsNotNotificationNotToSend = $notificationModel::find()
                                        ->select('notification.id')
                                        ->innerJoin('news', "notification.content_id = news.id AND notification.class_name = '" . addslashes($classname) . "'")
                                        ->innerJoin('news_categorie', 'news_categorie.id = news.news_categorie_id')
                                        ->andWhere(['notify_category' => 0]);
                                    $query->andWhere(['NOT IN', 'notification.id', $newsNotNotificationNotToSend]);
                                }
                            }

                            $model = new $classname;
                            if($model instanceof \open20\amos\core\interfaces\NotificationPersonalizedQueryInterface){
                                $queryModel = $model->getNotificationQuery($user ,$cwhActiveQuery);
                            } else {
                                $queryModel = $cwhActiveQuery->getQueryCwhOwnInterest();
                            }

                            if(!is_null($notify_editorial_staff) && $notify_editorial_staff == 0){
                                // 1 - puublication for all users
                                $queryModel->innerJoin('cwh_pubblicazioni', 'cwh_pubblicazioni.content_id = '.$classname::tableName().'.id AND cwh_pubblicazioni.cwh_config_contents_id = 1');
                                $queryModel->andWhere(['!=', 'cwh_pubblicazioni.cwh_regole_pubblicazione_id', 1]);
                            }
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
    //                Console::stdout($query->createCommand()->rawSql. PHP_EOL);
                    $result = $query->all();
                    if (!empty($result)) {
                        $builder->sendEmail([$uid], $result, false);
                        Console::stdout('Contents notified: ' . count($result) . PHP_EOL);

                    }
                    /** @var Notification $notify */
                    foreach ($result as $notify) {
                        $this->notifyReadFlag($notify->id, $uid);
                    }
                    Console::stdout('End working on user ' . $uid . PHP_EOL);
                    unset($query);
                }

                Console::stdout('---- ' . PHP_EOL);
                $transaction->commit();
                $transaction = null;
                gc_collect_cycles();
            }
        } catch (\Exception $e) {
            if(!is_null($transaction))
            {
                $transaction->rollBack();
            }
            throw $e;
        } catch (\Throwable $e) {
            if(!is_null($transaction))
            {
                $transaction->rollBack();
            }
            throw $e;
        }
    }
}
