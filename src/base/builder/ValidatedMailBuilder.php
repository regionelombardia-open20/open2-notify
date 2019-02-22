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
 * Class ValidatedMailBuilder
 * @package lispa\amos\notificationmanager\base\builder
 */
class ValidatedMailBuilder extends AMailBuilder
{
    /**
     * @inheritdoc
     */
    public function getSubject(array $resultset)
    {
        $stdMsg = AmosNotify::t('amosnotify', "Content has been validated");
        $model = reset($resultset);
        if ($model instanceof ModelLabelsInterface) {
            $grammar = $model->getGrammar();
            if (!is_null($grammar) && ($grammar instanceof ModelGrammarInterface)) {
                $stdMsg = AmosNotify::t('amosnotify', '#publication_email_subject', ['contentName' => $grammar->getModelSingularLabel()]);
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

        try {
            $userid = $model->getStatusLastUpdateUser($model->getValidatedStatus());
            if (!is_null($userid)) {
                $user = User::findOne($userid);
                $comment = $model->getStatusLastUpdateComment($model->getValidatedStatus());
                $controller = \Yii::$app->controller;
                $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/validated", [
                    'model' => $model,
                    'validator' => $user->getUserProfile()->one(),
                    'comment' => $comment
                ]);
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $ris;
    }
}
