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

use open20\amos\notificationmanager\AmosNotify;

$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
    $('#sql').on('click',function(e){
        e.preventDefault();
        if($(this).hasClass('nascosto')){
            $(this).show();
        }
    })
JS;

//$this->registerJs($js);


$labelsConf = \open20\amos\notificationmanager\models\NotificationsConfOpt::emailFrequencyValueAndLabels();
$userProfile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $user_id])->one();
if ($userProfile) {
    $nome = $userProfile->nomeCognome;
    $notifyConf = \open20\amos\notificationmanager\models\NotificationConf::find()->andWhere(['user_id' => $user_id])->one();
}
?>
<h5>
    <?= AmosNotify::t('amosnotify', "Riepilogo notifiche di <strong>{nome}</strong>", ['nome' => $nome]) ?>
    <br>
    <?= AmosNotify::t('amosnotify', "Destinatario riepilogo <strong>{email}</strong>", ['email' => $email]) ?>
</h5>
<?php if ($notifyConf) { ?>
    <br>
    <h5><?= "Configurazioni utente" ?></h5>
    <div class="row">
        <div class="col-md-6">
            <strong><?= 'Notifiche abilitate: ' ?></strong> <?= \Yii::$app->formatter->asBoolean($notifyConf->notifications_enabled) ?>
            <br><strong><?= 'Tipo riepilogo: ' ?></strong><?= $labelsConf[$notifyConf->email] ?>
            <br><strong><?= 'Ricevi notifiche di carattere generale: ' ?></strong> <?= \Yii::$app->formatter->asBoolean($userProfile->notify_from_editorial_staff) ?>
        </div>
        <div class="col-md-6">
            <strong><?= 'Notifica pubblicazione contenuti: ' ?></strong> <?= \Yii::$app->formatter->asBoolean($notifyConf->notify_content_pubblication) ?>
            <br><strong><?= 'Notifica commenti: ' ?></strong> <?= \Yii::$app->formatter->asBoolean($notifyConf->notify_comments) ?>
        </div>
    </div>
<?php } ?>
<br>

<?php echo \yii\helpers\Html::a('Mostra query sql', '#sql', ['data-toggle'=>"collapse", 'class' => 'btn btn-secondary']) ?>
<?php if (!empty($debugSqlNotifications[$user_id])) { ?>
    <br>
    <div id="sql" class="collapse">
        <h5><?= 'Query sql eseguita' ?></h5>
        <pre>
        <?= $debugSqlNotifications[$user_id]['generic'] ?>
    </pre>
        <?php foreach ($debugSqlNotifications[$user_id]['models'] as $classname => $sql) { ?>
            <h5><?= 'Query sql ' . $classname ?></h5>
            <pre>
            <?= $sql ?>
        </pre>
        <?php } ?>
    </div>
<?php } ?>

<?php if (!empty($text)) { ?>
    <br>
    <div>
        <h5><?= "Email inviata" ?></h5>
        <?= $text ?>
    </div>
<?php } else { ?>
    <br>
    <h5><?= 'Non è stata inviata nessuna email' ?></h5>
<?php }
?>

<?= \yii\helpers\Html::a('Indietro', ['/notify/notification-schedule/simulate'], ['class' => 'btn btn-secondary']) ?>

