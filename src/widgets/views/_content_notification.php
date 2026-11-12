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

use open20\amos\admin\models\UserProfile;
use open20\amos\notificationmanager\AmosNotify;
use yii\helpers\Html;

$notifyModule = Yii::$app->getModule('notify');
$modelsEnabled = [];
if ($notifyModule) {
    $modelsEnabled = $notifyModule->notificationConfContentEnabled;
}

if (!empty($modelsEnabled)) {
    $userId = UserProfile::findOne(Yii::$app->request->get('id'))->user_id;
    $notificationConf = \open20\amos\notificationmanager\models\NotificationConf::find()
        ->andWhere(['user_id' => $userId])->one();
    $values = [];
    $n_confs = 0;
    $notifications_enabled = false;
    // Load fields
    if ($notificationConf) {
        $notifications_enabled = $notificationConf->notifications_enabled;
        foreach ($notificationConf->notificationConfContents as $conf) {
            //$values[$conf->models_classname_id]['push'] = $conf->push_notification;
            $values[$conf->models_classname_id]['email'] = $conf->email;
            $n_confs++;
        }
    } ?>
    <div class="col-md-12">
        <?php foreach ($modelsEnabled as $classname) {
            $grammar = null;
            $modelClassname = \open20\amos\core\models\ModelsClassname::find()->andWhere(['classname' => $classname])->one();
            $object = new $classname;
            if ($object) {
                $grammar = $object->getGrammar();
            }

            if ($modelClassname && $grammar) { ?>
                <div class="row">
                    <div class="form-group col-xs-12">
                        <label class="control-label"> <?= \Yii::t('app', "Vuoi ricevere notifiche relative alla pubblicazione di nuove {x} nelle email di riepilogo?", [
                                'x' => $grammar->getModelLabel()
                            ]) ?>
                        </label>
                        <?= Html::radioList(
                            "notificationContent[$modelClassname->id][email]",
                            ($n_confs > 0) ? ((!empty($values[$modelClassname->id]['email'])) ? $values[$modelClassname->id]['email'] : 0) : 1,
                            [
                                1 => AmosNotify::t('amosnotify', 'Si'),
                                0 => AmosNotify::t('amosnotify', 'No')
                            ],
                            [
                                'id' => 'notificationContent',
                            ]
                        ) ?>
                    </div>
                </div>
                <?= \yii\helpers\Html::hiddenInput("notificationContent[$modelClassname->id][enable]", true) ?>
            <?php }
        } ?>
    </div>
<?php } ?>