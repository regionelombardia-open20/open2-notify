<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\base\builder
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

namespace open20\amos\notificationmanager\base\builder;

use open20\amos\core\interfaces\ModelGrammarInterface;
use open20\amos\core\interfaces\ModelLabelsInterface;
use open20\amos\core\user\User;
use open20\amos\notificationmanager\AmosNotify;

use Yii;

/**
 * Class ValidatorsMailBuilder
 * @package open20\amos\notificationmanager\base\builder
 */
class ValidatorsMailBuilder extends AMailBuilder
{
    /**
     * @inheritdoc
     */
    public function getSubject(array $resultset)
    {
        $stdMsg = AmosNotify::t('amosnotify', '#validation_request_email_subject');
        $model = reset($resultset);
        if ($model instanceof ModelLabelsInterface) {
            $grammar = $model->getGrammar();
            if (!is_null($grammar) && ($grammar instanceof ModelGrammarInterface)) {
                $stdMsg = AmosNotify::t('amosnotify', '#publication_request_email_subject', ['contentName' => $grammar->getModelSingularLabel()]);
            }
        }
        return $stdMsg;
    }

    /**
     * @inheritdoc
     */
    public function renderEmail(array $resultset, User $user)
    {
        $ris = "";
        $model = reset($resultset);
        $moduleMyActivities = Yii::$app->getModule('myactivities');
        $moduleNotify = Yii::$app->getModule('notify');
        $url = isset($moduleMyActivities) ? Yii::$app->urlManager->createAbsoluteUrl('myactivities/my-activities/index') : $model->getFullViewUrl();

        try {
            $viewValidatorPath = "@vendor/open20/amos-" . AmosNotify::getModuleName() . "/src/views/email/validator";
            if($moduleNotify && !empty($moduleNotify->viewPathEmailNotifyValidator) && !empty($moduleNotify->viewPathEmailNotifyValidator[get_class($model)])){
                $viewValidatorPath = $moduleNotify->viewPathEmailNotifyValidator[get_class($model)];
            }

            $controller = Yii::$app->controller;
            $view = $controller->renderPartial($viewValidatorPath, [
                'model' => $model,
                'url' => $url,
                'profile' => $user->userProfile
            ]);

            $ris = $this->renderView(\Yii::$app->controller->module->name, "validators_content_email", [
                'model' => $model,
                'url' => $url,
                'profile' => $user->userProfile,
                'original' => $view
            ]);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $ris;
    }
}
