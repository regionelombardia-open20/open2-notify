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

/**
 * @var $defaultLanguage string
 * @var $widget \open20\amos\notificationmanager\widgets\NotifyContentLanguageWidget
 */
?>
<div id="<?= $widget->id ?>" class="<?= $widget->class ?>">
    <div class="row">
        <label class="control-label"><?= \open20\amos\notificationmanager\AmosNotify::tHtml('amosnotify', 'Lingua del contenuto') ?></label>
        <?php
        echo \kartik\select2\Select2::widget([
            'id' => 'notify_content_language-id',
            'name' => 'notify_content_language',
            'data' => \yii\helpers\ArrayHelper::map(\lajax\translatemanager\models\Language::find()->andWhere(['status' => 1])->all(), 'language_id', 'language'),
            'value' => $value
        ]);
        ?>
    </div>
</div>