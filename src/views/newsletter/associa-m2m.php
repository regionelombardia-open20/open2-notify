<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\organizzazioni\views\profilo
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

use open20\amos\core\forms\editors\m2mWidget\M2MWidget;
use open20\amos\core\interfaces\NewsletterInterface;
use open20\amos\core\record\Record;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\controllers\NewsletterController;

/**
 * @var yii\web\View $this
 * @var \open20\amos\notificationmanager\models\Newsletter $model
 */

/** @var NewsletterController $appController */
$appController = Yii::$app->controller;

$newsletterConf = $appController->getNewsletterConf();
$confClassname = $newsletterConf->classname;

/** @var Record|NewsletterInterface $contentConfModel */
$contentConfModel = $newsletterConf->getContentConfModel();

$this->title = $appController->makeManageContentsTitle($contentConfModel);
$this->params['breadcrumbs'][] = $this->title;

?>

<?= M2MWidget::widget([
    'model' => $model,
    'modelId' => $model->id,
    'modelData' => $model->getNewsletterContentsByContentConfIdQuery($newsletterConf->id), // query degli elementi selezionati
    'modelDataArrFromTo' => [
        'from' => 'content_id',
        'to' => 'content_id'
    ],
    'modelTargetSearch' => [
        'class' => $confClassname,
        'query' => $appController->getAssociaM2mQuery($contentConfModel), // query generale di tutti gli elementi
    ],
    'gridId' => 'newsletter-grid',
    'viewSearch' => (isset($viewM2MWidgetGenericSearch) ? $viewM2MWidgetGenericSearch : false),
    'targetUrlController' => 'newsletter',
    'moduleClassName' => AmosNotify::className(),
    'targetColumnsToView' => $contentConfModel->newsletterSelectContentsGridViewColumns(),
]);
