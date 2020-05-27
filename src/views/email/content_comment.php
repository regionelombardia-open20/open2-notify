<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
use open20\amos\admin\widgets\UserCardWidget;
?>
    <div class="media-left">
        <?= UserCardWidget::widget(['model' => $comment->createdUserProfile, 'enableLink' => false]) ?>
    </div>
     <div class="clearfix"></div>
     <p class="answer_text"><?= Yii::$app->getFormatter()->asRaw($comment->comment_text) ?></p>
