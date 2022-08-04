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
use open20\amos\core\models\ModelsClassname;
use open20\amos\core\user\User; 
use open20\amos\cwh\AmosCwh;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\base\builder\AMailBuilder;
use open20\amos\notificationmanager\base\BuilderFactory;
use open20\amos\notificationmanager\models\base\NotificationSendEmail;
use open20\amos\notificationmanager\models\Notification;
use open20\amos\notificationmanager\models\NotificationChannels;
use open20\amos\notificationmanager\models\NotificationConf;
use open20\amos\notificationmanager\models\NotificationLanguagePreferences;
use open20\amos\notificationmanager\models\NotificationsConfOpt;
use open20\amos\notificationmanager\models\NotificationsRead;
use open20\amos\notificationmanager\utility\NotifyUtility;
use Exception;
use lajax\translatemanager\models\Language;
use Yii;
use yii\console\Controller;
use yii\db\Expression;
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

    const TYPE_OF_SECTION_NORMAL = 'normal';
    const TYPE_OF_SECTION_NETWORK = 'network';
    const TYPE_OF_SECTION_COMMENTS = 'comments';

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
            $module = \Yii::$app->getModule('notify');
            if ($module && $module->enableNotificationContentLanguage) {
                $this->mailChannelWithLanguage();
            } else {
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
            }
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }
    }

    /**
     *
     */
    public function mailChannelWithLanguage()
    {
        $type = $this->evaluateOperations();
        $languages = Language::find()->andWhere(['status' => 1])->all();
        Console::stdout('Begin mail-channel ' . $type . PHP_EOL);
        foreach ($languages as $language) {
            Console::stdout('     ' . PHP_EOL);
            Console::stdout('#################' . PHP_EOL);
            Console::stdout('Lingua: ' . $language->language_id . " ###" . PHP_EOL);
            Console::stdout('#################' . PHP_EOL);
            $users = $this->loadUser($type, $language);

            $factory = new BuilderFactory();
            if ($type == NotificationsConfOpt::EMAIL_IMMEDIATE) {
                Console::stdout('BUILD ' . $type . PHP_EOL);
                $builder = $factory->create(BuilderFactory::CONTENT_IMMEDIATE_MAIL_BUILDER);
            } else {
                $builder = $factory->create(BuilderFactory::CONTENT_MAIL_BUILDER);
            }
            /** @var AmosCwh $cwhModule */
            $cwhModule = Yii::$app->getModule('cwh');
            $this->notifyUserArray($cwhModule, $users, $builder, $type, $language->language_id);
        }
        Console::stdout('End mail-channel ' . $type . PHP_EOL);

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
            /** @var NotificationsRead $model */
            $model = $this->notifyModule->createModel('NotificationsRead');
            $model->notification_id = $notify_id;
            $model->user_id = $reader_id;
            $model->save(false);
        } catch (Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    /**
     * @param $schedule
     * @return array|null
     */
    protected function loadUser($schedule, $language = null)
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

            if ($module->enableNotificationContentLanguage) {
                $query->leftJoin('notification_language_preferences', 'notification_language_preferences.user_id = ' . UserProfile::tableName() . '.user_id');
                $query->andWhere(['OR',
                    ['notification_language_preferences.language' => $language->language_id],
                    ['notification_language_preferences.language' => null]
                ]);
            }

            $query->andWhere(['OR',
                [NotificationConf::tableName() . '.notifications_enabled' => 1],
                [NotificationConf::tableName() . '.notifications_enabled' => NULL],
            ]);
            $query->andWhere([UserProfile::tableName() . '.deleted_at' => null]);
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
           // $query->andWhere([UserProfile::tableName() . '.user_id' => 1]);
            $query->orderBy([UserProfile::tableName() . '.user_id' => SORT_ASC]);
            $query->select(UserProfile::tableName() . '.*');

            // query for network notificatiomn
            if (!empty($schedule)) {
                $queryConfCommunity
                    ->orderBy([UserProfile::tableName() . '.user_id' => SORT_ASC . ', notificationconf_network.*'])
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
            Yii::getLogger()->log($ex->getMessage(), Logger::LEVEL_ERROR);
        }
        return $result;
    }

    /**
     * @return int
     */
    protected function evaluateOperations()
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

        if (\Yii::$app->getModule('community')) {
            foreach ($comminities as $comminity_id) {
                Console::stdout('Start scope-mail-channel for community:' . $comminity_id . PHP_EOL);
                $community = \open20\amos\community\models\Community::findOne(['id' => $comminity_id]);
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
    }

    /**
     * @param AmosCwh|null $cwhModule
     * @param array $users
     * @param ContentMailBuilder $builder
     * @throws \yii\base\InvalidConfigException
     */
    private function notifyUserArray($cwhModule, $users, $builder, $type = null, $language = null)
    {
        $notifyLegacy = \Yii::$app->getModule('notify')->enableLegacyNotify;

        if ($notifyLegacy == true) {
            $this->legacyNotifyUserArray($cwhModule, $users, $builder, $type, $language);
        } else {
            $connection         = \Yii::$app->db;
            $transaction        = null;
            $typeOFnotification = [self::TYPE_OF_SECTION_NORMAL, self::TYPE_OF_SECTION_NETWORK];
            $results            = [];
            try {
                foreach ($users as $user) {
                    $transaction           = $connection->beginTransaction();
                    $uid                   = $user['user_id'];
                    /** @var NotificationConf $notificationConfModel */
                    $notificationConfModel = $this->notifyModule->createModel('NotificationConf');
                    /** @var  $notificationconf NotificationConf */
                    $notificationconf      = $notificationConfModel::find()->andWhere(['user_id' => $uid])->one();
                    Console::stdout('Start working on user '.$uid.PHP_EOL);

                    $isLanguageOk = $this->checkNotificationUserLanguage($uid, $language);
                    if ($language == null || (!empty($language) && $isLanguageOk)) {

                        /** @var  $notificationconf NotificationConf */
                        $notificationconf       = NotificationConf::find()->andWhere(['user_id' => $uid])->one();
                        $notify_editorial_staff = $user['notify_from_editorial_staff'];

                        if (!empty($notificationconf)) {
                            foreach ($typeOFnotification as $typeOfNotify) {
                                $query = $this->getNotifications($type, $typeOfNotify, $user);


                                // Get the netowrks to not notify
                                $notificationNetworkConfDontNotify = NotifyUtility::getNetworkNotificationConf($uid,
                                        $type);
                                $networkConfArray                  = [];
                                foreach ($notificationNetworkConfDontNotify as $networkConf) {
                                    $networkConfArray[$networkConf->models_classname_id] = $networkConf->record_id;
                                }
                                if (!empty($networkConfArray)) {
                                    foreach ($networkConfArray as $classname_id => $record_id) {

                                        if (!empty($classname_id) && !empty($record_id)) {
                                            $query->andWhere([
                                                'AND',
                                                ['!=', 'models_classname_id', $classname_id],
                                                ['!=', 'record_id', $record_id]
                                            ]);
                                        }
                                    }
                                }


                                if ($this->notifyModule->confirmEmailNotification) {
                                    $query->innerJoin(NotificationSendEmail::tableName(),
                                        'notification.class_name = notification_send_email.classname AND notification.content_id = notification_send_email.content_id');
                                }


                                if (isset($this->notifyModule->batchFromDate)) {
                                    $query->andWhere(['>=', Notification::tableName().'.created_at', strtotime($this->notifyModule->batchFromDate)]);
                                }


                                if (isset($cwhModule)) {
                                    $modelsEnabled = \open20\amos\cwh\models\CwhConfigContents::find()->addSelect('classname')->column();

                                    $andWhere = "";
                                    $i        = 0;
                                    foreach ($modelsEnabled as $classname) {
                                        $cwhActiveQuery               = new \open20\amos\cwh\query\CwhActiveQuery($classname,
                                            [
                                            'queryBase' => $classname::find(),
                                            'userId' => $uid
                                        ]);
                                        $cwhActiveQuery::$userProfile = null; //reset user profile

                                        /** if exist table news and module disable sending notification to certain types of news */
                                        $tableNews         = Yii::$app->db->schema->getTableSchema('news');
                                        $tableCategoryNews = Yii::$app->db->schema->getTableSchema('news_categorie');
                                        if (class_exists($classname) && $tableNews && $tableCategoryNews && isset($tableCategoryNews->columns['notify_category'])) {
                                            if ($classname == \open20\amos\news\models\News::className()) {
                                                /** @var Notification $notificationModel */
                                                $notificationModel            = $this->notifyModule->createModel('Notification');
                                                $newsNotNotificationNotToSend = $notificationModel::find()
                                                    ->select('notification.id')
                                                    ->innerJoin('news',
                                                        "notification.content_id = news.id AND notification.class_name = '".addslashes($classname)."'")
                                                    ->innerJoin('news_categorie',
                                                        'news_categorie.id = news.news_categorie_id')
                                                    ->andWhere(['notify_category' => 0]);
                                                $query->andWhere(['NOT IN', 'notification.id', $newsNotNotificationNotToSend]);
                                            }
                                        }

                                        $model = new $classname;
                                        if ($model instanceof \open20\amos\core\interfaces\NotificationPersonalizedQueryInterface) {
                                            $queryModel = $model->getNotificationQuery($user, $cwhActiveQuery);
                                        } else {
                                            $queryModel = $cwhActiveQuery->getQueryCwhOwnInterest();
                                        }

                                        if (!empty($language)) {
                                            $queryModel = $this->getNotificationContentLanguageQuery($queryModel,
                                                $classname, $language);
                                        }


                                        if (!is_null($notify_editorial_staff) && $notify_editorial_staff == 0) {
                                            // 1 - puublication for all users
                                            $queryModel->innerJoin('cwh_pubblicazioni',
                                                'cwh_pubblicazioni.content_id = '.$classname::tableName().'.id AND cwh_pubblicazioni.cwh_config_contents_id = 1');
                                            $queryModel->andWhere(['!=', 'cwh_pubblicazioni.cwh_regole_pubblicazione_id',
                                                1]);
                                        }


                                        $modelIds = $queryModel->select($classname::tableName().'.id')->column();
                                        if (!empty($modelIds)) {
                                            if ($i != 0) {
                                                $andWhere .= " OR ";
                                            }
                                            $andWhere .= '('.Notification::tableName().".class_name = '".addslashes($classname)."' AND ".Notification::tableName().'.content_id in ('.implode(',',
                                                    $modelIds).'))';
                                            $i++;
                                        }
                                        unset($cwhActiveQuery);
                                    }
                                    if (!empty($andWhere)) {
                                        $query->andWhere($andWhere);
                                    } else {
                                        Console::stdout('End working on user without interest '.$uid.PHP_EOL);
                                        $transaction->commit();

                                        continue 2;
                                    }
                                    //                Console::stdout($query->createCommand()->rawSql. PHP_EOL);
//                        die;

                                    $results [$typeOfNotify] = $query->all();
                                    Console::stdout($typeOfNotify.': '.count($results[$typeOfNotify]).PHP_EOL);
                                }
                            }
                            // get comments notification
                            $results [self::TYPE_OF_SECTION_COMMENTS] = $this->getNotifications($type,
                                    self::TYPE_OF_SECTION_COMMENTS, $user)->all();
                            Console::stdout(self::TYPE_OF_SECTION_COMMENTS.': '.count($results[self::TYPE_OF_SECTION_COMMENTS]).PHP_EOL);

                            if (!empty($results) && (!empty($results['normal']) || !empty($results['network']))) {

                                $builder->sendEmailMultipleSections([$uid], $results['normal'], $results['network'],
                                    $results['comments']);
                                Console::stdout('Contents notified: '.(count($results['normal']) + count($results['network'])).PHP_EOL);
                                foreach ($results as $result) {
                                    /** @var Notification $notify */
                                    foreach ($result as $notify) {
                                        $this->notifyReadFlag($notify->id, $uid);
                                    }
                                }
                            }
                            unset($query);
                            unset($queryModel);
                        }
                    }

                    Console::stdout('End working on user '.$uid.PHP_EOL);
                    Console::stdout('----------- '.PHP_EOL);

                    $transaction->commit();
                    $transaction = null;
                    gc_collect_cycles();
                }
            } catch (\Exception $e) {
                if (!is_null($transaction)) {
                    $transaction->rollBack();
                }
                throw $e;
            } catch (\Throwable $e) {
                if (!is_null($transaction)) {
                    $transaction->rollBack();
                }
                throw $e;
            }
        }
    }

    /**
     * @param $type
     * @param $user
     * @return \yii\db\ActiveQuery
     */
    private function getNotifications($type, $typeOfNotify, $user)
    {

        $orderByField = $this->getOrderModelsToNotify();
        $uid = $user['user_id'];
        $query = Notification::find()
            ->leftJoin(NotificationsRead::tableName(), ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName() . '.notification_id'), NotificationsRead::tableName() . '.user_id' => $uid])
            ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
            ->andWhere([NotificationsRead::tableName() . '.user_id' => null])
            ->andWhere(['>=', Notification::tableName() . ".created_at", new \yii\db\Expression("UNIX_TIMESTAMP('" . $user['created_at'] . "')")]);

        if ($typeOfNotify == self::TYPE_OF_SECTION_NORMAL) {
            $query->andWhere(['models_classname_id' => null, 'record_id' => null]);
            if (!empty($orderByField)) {
                $query->orderBy(new Expression('FIELD(class_name, ' . $orderByField . ') DESC, class_name'));
            } else {
                $query->orderBy('class_name');
            }

        } else if ($typeOfNotify == self::TYPE_OF_SECTION_NETWORK) {
            $query->andWhere(['IS NOT', 'models_classname_id', null])
                ->andWhere(['IS NOT', 'record_id', null]);
            if (!empty($orderByField)) {
                $query->orderBy(new Expression('models_classname_id ASC, record_id ASC, ' . 'FIELD(class_name, ' . $orderByField . ') DESC, class_name ASC'));
            } else {
                $query->orderBy('models_classname_id ASC, record_id ASC, class_name ASC');
            }
        } else if ($typeOfNotify == self::TYPE_OF_SECTION_COMMENTS) {
            $query = Notification::find()
                ->leftJoin(NotificationsRead::tableName(), ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName() . '.notification_id'), NotificationsRead::tableName() . '.user_id' => $uid])
                ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
                ->andWhere([NotificationsRead::tableName() . '.user_id' => null])
                ->andWhere([Notification::tableName() . '.class_name' => \open20\amos\comments\models\Comment::className()])
                ->andWhere(['>=', Notification::tableName() . ".created_at", new \yii\db\Expression("UNIX_TIMESTAMP('" . $user['created_at'] . "')")]);
        }
        return $query;
//        BY FIELD(class_name, 'open20\\amos\\events\\models\\Event', 'open20\\amos\\news\\models\\News', 'open20\\amos\\partnershipprofiles\\models\PartnershipProfiles', 'open20\\amos\\discussioni\\models\\DiscussioniTopic'), class_name

    }


    private function getOrderModelsToNotify()
    {
        $module = \Yii::$app->getModule('notify');
        $orderByField = '';
        if ($module && !empty($module->orderEmailSummary)) {
            $escapedClassnames = [];
            foreach ($module->orderEmailSummary as $classname) {
                $escapedClassnames [] = addslashes($classname);
            }
            $orderByField = ("'" . implode("','", array_reverse($escapedClassnames)) . "'");
        }
        return $orderByField;
    }

    /**
     * @param $queryModel
     * @param $classname
     * @param $language
     * @return mixed
     */
    private function getNotificationContentLanguageQuery($queryModel, $classname, $language)
    {
        $modelsclassname = ModelsClassname::find()->andWhere(['classname' => $classname])->one();
        if ($modelsclassname) {
            $queryModel->leftJoin('notification_content_language', 'notification_content_language.record_id = ' . $classname::tableName() . '.id')
                ->andWhere(['models_classname_id' => $modelsclassname->id])
                ->andWhere(['language' => $language]);
        }
//        if($modelsclassname->id == 6){
//            Console::stdout( $queryModel->createCommand()->rawSql . PHP_EOL);
//        }
//        if($modelsclassname->module == 'news'){
//            Console::stdout( $queryModel->createCommand()->rawSql . PHP_EOL);
//
//        }
        return $queryModel;
    }

    /**
     * @param $user_id
     * @param $language
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    private function checkNotificationUserLanguage($user_id, $language)
    {
        $languagePreferences = NotificationLanguagePreferences::find()->andWhere(['user_id' => $user_id])->all();
        if (count($languagePreferences) == 0) {
            $moduleTranslation = \Yii::$app->getModule('translations');
            if ($moduleTranslation) {
                $lang = $moduleTranslation->getUserLanguage($user_id);
                return $lang == $language;
            } else {
                return \Yii::$app->language == $language;
            }
        } else {
            foreach ($languagePreferences as $pref) {
//                Console::stdout( $pref->language .' check ' .$language . PHP_EOL);
                if ($pref->language == $language) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @param AmosCwh|null $cwhModule
     * @param array $users
     * @param AMailBuilder $builder
     * @throws \yii\base\InvalidConfigException
     */
    private function legacyNotifyUserArray($cwhModule, $users, $builder, $type = null)
    {
        $connection  = \Yii::$app->db;
        $transaction = null;
        try {
            foreach ($users as $user) {
                $transaction            = $connection->beginTransaction();
                $uid                    = $user['user_id'];
                /** @var NotificationConf $notificationConfModel */
                $notificationConfModel  = $this->notifyModule->createModel('NotificationConf');
                /** @var  $notificationconf NotificationConf */
                $notificationconf       = $notificationConfModel::find()->andWhere(['user_id' => $uid])->one();
                Console::stdout('Start working on user '.$uid.PHP_EOL);
                $notify_editorial_staff = $user['notify_from_editorial_staff'];

                if (!empty($notificationconf)) {
                    /** @var Notification $notificationModel */
                    $notificationModel = $this->notifyModule->createModel('Notification');
                    $query             = $notificationModel::find()
                        ->leftJoin(NotificationsRead::tableName(),
                            ['notification.id' => new \yii\db\Expression(NotificationsRead::tableName().'.notification_id'),
                            NotificationsRead::tableName().'.user_id' => $uid])
                        ->andWhere(['channels' => NotificationChannels::CHANNEL_MAIL])
                        ->andWhere([NotificationsRead::tableName().'.user_id' => null])
                        ->andWhere(['>=', Notification::tableName().".created_at", new \yii\db\Expression("UNIX_TIMESTAMP('".$user['created_at']."')")]);

                    // Get the netowrks to not notify
                    $notificationNetworkConfDontNotify = NotifyUtility::getNetworkNotificationConf($uid, $type);
                    $networkConfArray                  = [];
                    foreach ($notificationNetworkConfDontNotify as $networkConf) {
                        $networkConfArray[$networkConf->models_classname_id] = $networkConf->record_id;
                    }
                    if (!empty($networkConfArray)) {
                        foreach ($networkConfArray as $classname_id => $record_id) {

                            if (!empty($classname_id) && !empty($record_id)) {
                                $query->andWhere([
                                    'AND',
                                    ['!=', 'models_classname_id', $classname_id],
                                    ['!=', 'record_id', $record_id]
                                ]);
                            }
                        }
                    }


                    if ($this->notifyModule->confirmEmailNotification) {
                        $query->innerJoin(NotificationSendEmail::tableName(),
                            'notification.class_name = notification_send_email.classname AND notification.content_id = notification_send_email.content_id');
                    }


                    if (isset($this->notifyModule->batchFromDate)) {
                        $query->andWhere(['>=', Notification::tableName().'.created_at', strtotime($this->notifyModule->batchFromDate)]);
                    }

                    if (isset($cwhModule)) {
                        $modelsEnabled = \open20\amos\cwh\models\CwhConfigContents::find()->addSelect('classname')->column();
                        $andWhere      = "";
                        $i             = 0;
                        foreach ($modelsEnabled as $classname) {
                            $cwhActiveQuery               = new \open20\amos\cwh\query\CwhActiveQuery($classname,
                                [
                                'queryBase' => $classname::find(),
                                'userId' => $uid
                            ]);
                            $cwhActiveQuery::$userProfile = null; //reset user profile

                            /** if exist table news and module disable sending notification to certain types of news */
                            $tableNews         = Yii::$app->db->schema->getTableSchema('news');
                            $tableCategoryNews = Yii::$app->db->schema->getTableSchema('news_categorie');
                            if (class_exists($classname) && $tableNews && $tableCategoryNews && isset($tableCategoryNews->columns['notify_category'])) {
                                if ($classname == \open20\amos\news\models\News::className()) {
                                    /** @var Notification $notificationModel */
                                    $notificationModel            = $this->notifyModule->createModel('Notification');
                                    $newsNotNotificationNotToSend = $notificationModel::find()
                                        ->select('notification.id')
                                        ->innerJoin('news',
                                            "notification.content_id = news.id AND notification.class_name = '".addslashes($classname)."'")
                                        ->innerJoin('news_categorie', 'news_categorie.id = news.news_categorie_id')
                                        ->andWhere(['notify_category' => 0]);
                                    $query->andWhere(['NOT IN', 'notification.id', $newsNotNotificationNotToSend]);
                                }
                            }

                            $model = new $classname;
                            if ($model instanceof \open20\amos\core\interfaces\NotificationPersonalizedQueryInterface) {
                                $queryModel = $model->getNotificationQuery($user, $cwhActiveQuery);
                            } else {
                                $queryModel = $cwhActiveQuery->getQueryCwhOwnInterest();
                            }

                            if (!is_null($notify_editorial_staff) && $notify_editorial_staff == 0) {
                                // 1 - puublication for all users
                                $queryModel->innerJoin('cwh_pubblicazioni',
                                    'cwh_pubblicazioni.content_id = '.$classname::tableName().'.id AND cwh_pubblicazioni.cwh_config_contents_id = 1');
                                $queryModel->andWhere(['!=', 'cwh_pubblicazioni.cwh_regole_pubblicazione_id', 1]);
                            }
                            $modelIds = $queryModel->select($classname::tableName().'.id')->column();
                            if (!empty($modelIds)) {
                                if ($i != 0) {
                                    $andWhere .= " OR ";
                                }
                                $andWhere .= '('.Notification::tableName().".class_name = '".addslashes($classname)."' AND ".Notification::tableName().'.content_id in ('.implode(',',
                                        $modelIds).'))';
                                $i++;
                            }
                            unset($cwhActiveQuery);
                        }
                        if (!empty($andWhere)) {
                            $query->andWhere($andWhere);
                        } else {
                            Console::stdout('End working on user without interest'.$uid.PHP_EOL);
                            $transaction->commit();
                            $transaction = null;
                            continue;
                        }
                    }
                    $query->orderBy('class_name');
                    //                Console::stdout($query->createCommand()->rawSql. PHP_EOL);
                    $result = $query->all();
                    if (!empty($result)) {
                        $builder->sendEmailLegacy([$uid], $result, false);
                        Console::stdout('Contents notified: '.count($result).PHP_EOL);
                    }
                    /** @var Notification $notify */
                    foreach ($result as $notify) {
                        $this->notifyReadFlag($notify->id, $uid);
                    }
                    Console::stdout('End working on user '.$uid.PHP_EOL);
                    unset($query);
                }

                Console::stdout('---- '.PHP_EOL);
                $transaction->commit();
                $transaction = null;
                gc_collect_cycles();
            }
        } catch (\Exception $e) {
            if (!is_null($transaction)) {
                $transaction->rollBack();
            }
            throw $e;
        } catch (\Throwable $e) {
            if (!is_null($transaction)) {
                $transaction->rollBack();
            }
            throw $e;
        }
    }
}
