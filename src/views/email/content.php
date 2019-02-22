<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\views\email
 * @category   CategoryName
 */

use lispa\amos\core\helpers\Html;
use lispa\amos\core\interfaces\ContentModelInterface;
use lispa\amos\core\interfaces\ViewModelInterface;
use lispa\amos\core\record\Record;
use lispa\amos\cwh\base\ModelContentInterface;

/**
 * @var Record|ContentModelInterface|ViewModelInterface $model
 * @var \lispa\amos\admin\models\UserProfile $profile
 */

if (!empty($profile)) {
    $this->params['profile'] = $profile;
}

?>

<div style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff;">

    <div style="padding:0;margin:0">
        <h3 style="font-size:2em;line-height: 1;margin:0;padding:10px 0;">
            <?= Html::a($model->getTitle(), Yii::$app->urlManager->createAbsoluteUrl($model->getFullViewUrl()), ['style' => 'color: #297A38;']) ?>
        </h3>
    </div>
    <div style="box-sizing:border-box;font-size:13px;font-weight:normal;color:#000000;">
        <?= $model->getDescription(true); ?>
    </div>
    <div style="box-sizing:border-box;padding-bottom: 5px;">
        <div style="margin-top:20px; display: flex; padding: 10px;">
            <div style="width: 50px; height: 50px; overflow: hidden;-webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                <?php
                $layout = '{publisher}';
                if ($model instanceof ModelContentInterface) {
                    $layout = '{publisher}{publishingRules}{targetAdv}';
                }
                $user = $model->createdUserProfile;
                ?>

                <?php if (!is_null($user)): ?>
                    <?= \lispa\amos\admin\widgets\UserCardWidget::widget([
                        'model' => $user,
                        'onlyAvatar' => true,
                        'absoluteUrl' => true
                    ])
                    ?>
                <?php endif; ?>
            </div>

            <div style="margin-left: 20px; max-width: 430px;">
                <?= \lispa\amos\core\forms\PublishedByWidget::widget([
                    'model' => $model,
                    'layout' => $layout,
                ]) ?>
            </div>
        </div>
    </div>
</div>