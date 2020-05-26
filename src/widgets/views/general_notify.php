<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
/**
 * @var $widget \open20\amos\notificationmanager\widgets\NotifyFrequencyAdvancedWidget
 * @var $notificationConf \open20\amos\notificationmanager\models\NotificationConf
 */

use yii\helpers\Html;
use open20\amos\notificationmanager\AmosNotify;

?>
<?php
$js = <<<JS
        if($("input:radio[name='notifications_enabled']:checked").val() === "1"){
            $('#container-nofification-enabled').show();
        }

        $('#notifications-enabled').click(function(){
                var valueNotifyEnabled = $("input:radio[name='notifications_enabled']:checked").val();
                if(valueNotifyEnabled == 1){
                    $('#container-nofification-enabled').show();
                }
                else {
                    $('#container-nofification-enabled').hide();
                    $('#notify-content-pubblication input[value="1"]').removeAttr('checked');
                    $('#notify-content-pubblication input[value="0"]').attr('checked', true);

                    $('#notify-comments input[value="1"]').removeAttr('checked');
                    $('#notify-comments input[value="0"]').attr('checked', true);
                }
        });
JS;
$this->registerJs($js);

?>
<div class="col-xs-12">
    <div class="form-group col-xs-6 nop">
        <label class="control-label"><?= AmosNotify::t('amosnotify', 'Vuoi ricevere notifiche di aggiornamento dalla piattaforma {NomePiattaforma} ?', ['NomePiattaforma' => \Yii::$app->name]) ?></label>
        <?= Html::radioList('notifications_enabled', $notificationConf->notifications_enabled, [1 => AmosNotify::t('amosnotify', 'Si'), 0 => AmosNotify::t('amosnotify', 'No')], [
                'id' => 'notifications-enabled'
            ]
        ) ?>
    </div>
</div>

<div id="container-nofification-enabled" style="display:none">
    <div class="form-group col-xs-12"> <?= $htmlFrequencySelector ?></div>
    <div class="form-group col-xs-12">
        <div class="checkbox">
            <?php echo \open20\amos\core\helpers\Html::activeCheckbox($widget->model, 'notify_from_editorial_staff', [
                'name' => 'notify_from_editorial_staff',
                'id' => 'notify_from_editorial_staff-1',
                'onchange' => "if(!$(this).is(':checked')){ $('#notify-uncheck').modal('show'); }"
            ]) ?>
        </div>
    </div>
    <div class="form-group col-xs-12">
        <label class="control-label"><?= AmosNotify::t('amosnotify', 'Vuoi ricevere aggiornamenti di avvenuta pubblicazione contenuto?') ?></label>
        <?= Html::radioList('notify_content_pubblication', $notificationConf->notify_content_pubblication, [1 => AmosNotify::t('amosnotify', 'Si'), 0 => AmosNotify::t('amosnotify', 'No')], [
                'id' => 'notify-content-pubblication',
            ]
        ) ?>
        <!--        --><?php //Html::checkbox('notify_content_pubblication', $notificationConf->notify_content_pubblication,
        //            [
        //                'label' => AmosNotify::t('amosnotify', ''),
        //                'id' => 'notify-content-pubblication'
        //            ]
        //        ); ?>
    </div>
    <div class="form-group col-xs-12">
        <label class="control-label"><?= AmosNotify::t('amosnotify', 'Vuoi ricevere aggiornamenti di pubblicazione di un contributo per un contenuto di tuo interesse?') ?></label>
        <?= Html::radioList('notify_comments', $notificationConf->notify_comments, [1 => AmosNotify::t('amosnotify', 'Si'), 0 => AmosNotify::t('amosnotify', 'No')], [
                'id' => 'notify-comments'
            ]
        ) ?>
        <!--        --><?php //Html::checkbox('notify_comments', $notificationConf->notify_comments,
        //            [
        //                'label' => AmosNotify::t('amosnotify', 'Vuoi ricevere aggiornamenti di pubblicazione di un contributo per un contenuto di tuo interesse?', ['NomePiattaforma'=> \Yii::$app->name]),
        //                 'id' => 'notify-comments',
        //            ]
        //        ); ?>
    </div>

    <?php
    if (!empty($dataProviderNetwork)) {
        echo "<p>" . AmosNotify::t('amosnotify',
                'Con quale frequenza desideri ricevere aggiornamenti dalla piattaforma per la pubblicazione di un nuovo contenuto di tuo interesse all’interno delle community?') . "</p>";
        echo \open20\amos\core\views\AmosGridView::widget([
            'dataProvider' => $dataProviderNetwork,
            'columns' => [
                'logo_id' => [
                    'headerOptions' => [
                        'id' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Logo'),
                    ],
                    'contentOptions' => [
                        'headers' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Logo'),
                    ],
                    'label' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Logo'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return \open20\amos\community\widgets\CommunityCardWidget::widget(['model' => $model]);
                    }
                ],
                [
                    'attribute' => 'name',
                    'format' => 'html',
                    'value' => function ($model) {
                        /** @var Community $model */
                        return Html::a($model->name, ['/community/community/view', 'id' => $model->id], [
                            'title' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Apri il profilo della community {community_name}', ['community_name' => $model->name])
                        ]);
                    }
                ],
                'communityType' => [
                    'attribute' => 'communityType',
                    'format' => 'html',
                    'value' => function ($model) {
                        /** @var Community $model */
                        if (!is_null($model->community_type_id)) {
                            return \open20\amos\community\AmosCommunity::t('amoscommunity', $model->communityType->name);
                        } else {
                            return '-';
                        }
                    }
                ],
                [
                    'label' => AmosNotify::t('amosnotify', 'Frequency'),
                    'value' => function ($model) use ($widgetConfData, $notificationNetworkValues) {
                        return \kartik\select2\Select2::widget([
                            'data' => $widgetConfData,
                            'name' => 'notifyCommunity[' . $model->id . ']',
                            'value' => !empty($notificationNetworkValues[$model->id]) ? $notificationNetworkValues[$model->id] : null,
                            'options' => [
                                'lang' => substr(\Yii::$app->language, 0, 2),
                                'multiple' => false,
                                'placeholder' => AmosNotify::t('amosnotify', 'Select/Choose') . '...',
                            ]
                        ]);
                    },
                    'format' => 'raw'
                ],
//                'created_by' => [
//                    'attribute' => 'created_by',
//                    'format' => 'html',
//                    'value' => function($model){
//                        /** @var Community $model */
//                        $name = '-';
//                        if(!is_null($model->created_by)) {
//                            $creator = \open20\amos\core\user\User::findOne($model->created_by);
//                            if(!empty($creator)) {
//                                return $creator->getProfile()->getNomeCognome();
//                            }
//                        }
//                        return $name;
//                    }
//                ],
//                'status' => [
//                    'attribute' => 'status',
//                    'label' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Status'),
//                    'headerOptions' => [
//                        'id' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Status'),
//                    ],
//                    'contentOptions' => [
//                        'headers' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Status'),
//                    ],
//                    'value' => function($model)use ($widget){
//                        /** @var Community $model */
//                        $mmrow = \open20\amos\community\models\CommunityUserMm::findOne(['user_id' => $widget->model->user_id, 'community_id' => $model->id]);
//                        return  $mmrow->status;
//                    }
//                ],
//                'role' => [
//                    'attribute' => 'role',
//                    'label' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Role'),
//                    'headerOptions' => [
//                        'id' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Role'),
//                    ],
//                    'contentOptions' => [
//                        'headers' => \open20\amos\community\AmosCommunity::t('amoscommunity', 'Role'),
//                    ],
//                    'value' => function($model) use ($widget){
//                        /** @var Community $model */
//                        $mmrow = \open20\amos\community\models\CommunityUserMm::findOne(['user_id' =>  $widget->model->user_id, 'community_id' => $model->id]);
//                        return  $mmrow->role;
//                    }
//                ],
            ]
        ]);
    }
    ?>
</div>


