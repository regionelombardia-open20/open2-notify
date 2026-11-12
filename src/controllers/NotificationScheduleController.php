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

namespace open20\amos\notificationmanager\controllers;

use open20\amos\core\controllers\AmosController;
use open20\amos\core\controllers\BackendController;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\module\BaseAmosModule;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\base\BuilderFactory;
use open20\amos\notificationmanager\commands\NotifierController;
use open20\amos\notificationmanager\models\Notification;
use open20\amos\notificationmanager\models\NotificationSchedule;
use open20\amos\notificationmanager\models\NotificationsConfOpt;
use open20\amos\notificationmanager\models\search\NotificationScheduleSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class NotificationScheduleController extends CrudController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                            'detail',
                            'users-notified',
                            'simulate'
                        ],
                        'roles' => ['NOTIFY_SCHEDULE_ADMINISTRATOR']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->setModelObj(new NotificationSchedule());
        $this->setModelSearch(new NotificationSchedule());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Table')),
                'url' => '?currentView=grid'
            ],
        ]);

        parent::init();

        $this->setUpLayout();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        $titleSection = AmosNotify::t('amosnotify', 'Notifiche notturne');
        $labelLinkAll = AmosNotify::t('amosnotify', 'Tutte le notifiche');
        $urlLinkAll = '/news/news/all-news';
        $titleLinkAll = AmosNotify::t('amosnotify', 'Visualizza la lista delle notifiche');

        $subTitleSection = Html::tag('p', AmosNotify::t('amosnotify', '#beforeActionSubtitleSectionLogged'));


        $this->view->params = [
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'notify',
            'titleSection' => $titleSection,
            'hideCreate' => true
        ];

        // Lasciare qui questo if e return perché se no va in loop...
        if (!parent::beforeAction($action)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($layout = null)
    {
        $this->setUpLayout('list');
        $btnSimulate = Html::a(AmosNotify::t('amosnotify', 'Simula invio'), '/notify/notification-schedule/simulate', [
            'class' => 'btn btn-secondary btn-xs',
            'title' => AmosNotify::t('amosnotify', 'Simula invio')
        ]);
        \Yii::$app->view->params['additionalButtons'] = [
            'htmlButtons' => [$btnSimulate]
        ];
        $modelSearch = new NotificationScheduleSearch();
        $dataProvider = $modelSearch->search(\Yii::$app->request->get());
//        pr(\Yii::$app->request->get());
        return $this->render('@vendor/open20/amos-notify/src/views/notification-schedule/index', ['dataProvider' => $dataProvider, 'modelSearch' => $modelSearch]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDetail($id)
    {
        \Yii::$app->controller->view->params['disable-search'] = true;
        $this->setUpLayout('list');
        $model = NotificationSchedule::findOne($id);
        $this->view->params['titleSection'] = AmosNotify::t('amosnotify', 'Contenuti notifica delle {time}', ['time' => $model->created_at]);

        $dataProvider = new ActiveDataProvider([
            'query' => $model->getNotificationScheduleContents()
        ]);
        return $this->render('@vendor/open20/amos-notify/src/views/notification-schedule/detail', ['dataProvider' => $dataProvider]);
    }

    /**
     * @param $notification_id
     * @param $schedule_id
     * @return string
     */
    public function actionUsersNotified($notification_id, $schedule_id)
    {
        $this->setUpLayout('list');
        $modelSearch = new NotificationScheduleSearch();
        $modelSearch->notification_id = $notification_id;
        $schedule = NotificationSchedule::findOne($schedule_id);
        $dataProvider = $modelSearch->searchUsers(\Yii::$app->request->get());
//        pr(\Yii::$app->request->get());
        return $this->render('@vendor/open20/amos-notify/src/views/notification-schedule/users', [
            'dataProvider' => $dataProvider,
            'modelSearch' => $modelSearch,
            'schedule_id' => $schedule_id,
            'schedule' =>$schedule
        ]);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSimulate()
    {
        $this->setUpLayout('list');
        \Yii::$app->controller->view->params['disable-search'] = true;
        $this->view->params['titleSection'] = AmosNotify::t('amosnotify', 'Simulatore invio email');

        $notifierController = new NotifierController('notifier', $this->module);
        $moduleCwh = \Yii::$app->getModule('cwh');

        if (\Yii::$app->request->get()) {
            $cwhModule = \Yii::$app->getModule('cwh');
            $user_ids = \Yii::$app->request->get('user_ids');
            $email = \Yii::$app->request->get('email');
            if (!empty($user_ids) && !empty($email)) {
                $notifierController->notifyModule->enableSegmentedSend = false;
                $factory = new BuilderFactory();
                $builder = $factory->create(BuilderFactory::CONTENT_MAIL_CACHED_BUILDER);
                $builder->simulateEmailReceiver = $email;
                $users = $notifierController->loadUser(NotificationsConfOpt::EMAIL_DAY, null, 0, 0, false, [$user_ids]);
                $notifierController->notifyUserCached($cwhModule, $users, $builder, NotificationsConfOpt::EMAIL_DAY, null, null, null, true);
                $debugSqlNotifications = $notifierController->debugSqlNotifications;
                return $this->render('email-test', [
                    'text' => $builder->outputEmailText,
                    'user_id' => $user_ids,
                    'debugSqlNotifications' => $debugSqlNotifications,
                    'email' => $email
                ]);
            }

        } else {
            $modelsEnabled = $notifierController->getModelsEnabled($moduleCwh);
            $query = $notifierController->getAllNotificationRecordsQuery($modelsEnabled, null, null);
            $dataProvider = new ActiveDataProvider([
                'query' => $query
            ]);
            $sql = $query->createCommand()->rawSql;
        }

        return $this->render('@vendor/open20/amos-notify/src/views/notification-schedule/simulate', [
            'dataProvider' => $dataProvider,
            'sql' => $sql
        ]);
    }


}