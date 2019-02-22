<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\base\builder
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\base\builder;

use lispa\amos\core\interfaces\ModelGrammarInterface;
use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\user\User;
use lispa\amos\notificationmanager\AmosNotify;
use Yii;

/**
 * Class ValidatorsMailBuilder
 * @package lispa\amos\notificationmanager\base\builder
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
        $url = isset($moduleMyActivities) ? Yii::$app->urlManager->createAbsoluteUrl('myactivities/my-activities/index') : $model->getFullViewUrl();

        try {
            $controller = Yii::$app->controller;
            $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/validator", [
                'model' => $model,
                'url' => $url,
                'profile' => $user->userProfile
            ]);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $ris;
    }
}
