<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\views\newsletter
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

use open20\amos\core\helpers\Html;
use open20\amos\core\module\BaseAmosModule;
use open20\amos\core\views\DataProviderView;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\assets\NotifyAsset;
use open20\amos\notificationmanager\models\Newsletter;
use open20\amos\notificationmanager\widgets\SendNewsletterWidget;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\notificationmanager\models\search\NewsletterSearch $model
 * @var string $currentView
 */

$this->params['breadcrumbs'][] = $this->title;


?>
<div class="notification-index">
    <?= \open20\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'value' => function ($model) {
                    $notification = $model->notification;
                    if ($notification) {
                        $class = $notification->class_name;
                        $modelClassname = \open20\amos\core\models\ModelsClassname::find()->andWhere(['classname' => $class])->one();
                        if ($modelClassname) {
                            return $modelClassname->label;
                        } else {
                            return $class;
                        }
                    }
                    return ' - ';
                },
                'label' => 'Contenuto'
            ],
            [
                'attribute' => 'notification_id',
                'value' => function ($model) {
                    $notification = $model->notification;
                    if ($notification) {
                        $class = $notification->class_name;
                        $record_id = $notification->content_id;
                        $contentModel = $class::findOne($record_id);
                        if ($contentModel && method_exists($contentModel, 'getTitle')) {
                            return $contentModel->getTitle();
                        }
                    }
                    return ' - ';
                },
                'label' => 'Titolo'
            ],
            [
                'value' => function ($model) {
                    $notification = $model->notification;
                    if ($notification) {
                        $modelClassname = \open20\amos\core\models\ModelsClassname::findOne($notification->models_classname_id);
                        if ($modelClassname) {
                            $Class = $modelClassname->classname;
                            $network = $Class::findOne($notification->record_id);
                            if ($network && method_exists($network, 'getTitle')) {
                                return $network->id . ' - ' . $network->getTitle();
                            }
                        }
                    }
                    return ' - ';
                },
                'label' => 'Dalla community'
            ],
            [
                'class' => 'open20\amos\core\views\grid\ActionColumn',
                'template' => '{view}{users}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $notification = $model->notification;
                        if ($notification) {
                            $class = $notification->class_name;
                            $record_id = $notification->content_id;
                            $contentModel = $class::findOne($record_id);
                            if ($contentModel) {
                                return Html::a(\open20\amos\core\icons\AmosIcons::show('file'), $contentModel->getFullViewUrl(), [
                                    'class' => 'btn btn-tools-secondary',
                                    'title' => 'Vedi contenuto'
                                ]);
                            }
                        }
                        return '';
                    },
                    'users' => function ($url, $model, $key) {
                        return Html::a(\open20\amos\core\icons\AmosIcons::show('accounts'),
                            ['/notify/notification-schedule/users-notified','notification_id' => $model->notification_id, 'schedule_id' => $model->notification_schedule_id],
                            [
                            'class' => 'btn btn-tools-secondary',
                            'title' => 'vedi utenti'
                        ]);
                    },
                ]
            ]

        ]
    ]); ?>
    <?= Html::a('Indietro', ['/notify/notification-schedule/index'], ['class' => 'btn btn-secondary']) ?>
</div>
