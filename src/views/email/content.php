<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\views\email
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

use open20\amos\core\forms\ItemAndCardHeaderWidget;
use open20\amos\core\helpers\Html;
use open20\amos\core\interfaces\ContentModelInterface;
use open20\amos\core\interfaces\ViewModelInterface;
use open20\amos\core\record\Record;

/**
 * @var Record|ContentModelInterface|ViewModelInterface $model
 * @var \open20\amos\admin\models\UserProfile $profile
 */

if (!empty($profile)) {
    $this->params['profile'] = $profile;
}
$notifyModule = \open20\amos\notificationmanager\AmosNotify::instance();
?>

<div style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff;">
    <div style="padding:0;margin:0">
        <h3 style="font-size:2em;line-height: 1;margin:0;padding:10px 0;">
            <?= Html::a($model->getTitle(), Yii::$app->urlManager->createAbsoluteUrl($model->getFullViewUrl()), ['style' => 'color: '.$notifyModule->mailThemeColor['bgPrimary'] .';']) ?>        </h3>
    </div>
    <div style="box-sizing:border-box;font-size:13px;font-weight:normal;color:#000000;">
        <?= $model->getDescription(true); ?>
    </div>
    <div style="box-sizing:border-box;padding-bottom: 5px;">
        <div style="margin-top:20px; display: flex; padding: 10px;">
            <?= ItemAndCardHeaderWidget::widget([
                'model' => $model,
                'publicationDateNotPresent' => true,
                'showPrevalentPartnershipAndTargets' => true,
                'absoluteUrlAvatar' => true,
            ]); ?>
        </div>
    </div>
</div>
