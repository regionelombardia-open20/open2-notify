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

echo $this->render('_search', ['model' => $modelSearch]);
?>
<div class="notification-index">
    <?= \open20\amos\core\views\AmosGridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'status',
                'label' => 'Stato'
            ],
            [
                'label' => 'Creato il',
                'value' => function ($model) {
                    if (!empty($model->created_at)) {
                        $date =  new \DateTime($model->created_at);
                        return $date->format( 'd/m/Y H:i');
                    }
                    return '';
                }
            ],
            [
                'label' => 'Terminato il',
                'value' => function ($model) {
                    if (!empty($model->ended_at)) {
                        $date = new \DateTime($model->ended_at);
                        return $date->format( 'd/m/Y H:i');
                    }
                    return '-';
                }

            ],
            [
                'label' => AmosNotify::t('amosnotify', 'Utenti'),
                'value' => function ($model) {
                    return $model->last_notified_user_id.'/'.$model->max_user_id_to_notify;
                },
            ],
            [
                'label' => AmosNotify::t('amosnotify', '#total_contents'),
                'value' => function ($model) {
                    return $model->getNotificationScheduleContents()->count();
                },
            ],

            [
                'class' => 'open20\amos\core\views\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a(\open20\amos\core\icons\AmosIcons::show('file'), ['/notify/notification-schedule/detail', 'id' => $model->id], [
                            'class' => 'btn btn-tools-secondary',
                            'title' => 'Dettaglio invio'
                        ]);
                    },
                ]
            ]
        ]
    ]); ?>
</div>
