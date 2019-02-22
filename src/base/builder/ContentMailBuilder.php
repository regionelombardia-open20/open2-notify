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

use lispa\amos\core\interfaces\ModelLabelsInterface;
use lispa\amos\core\record\Record;
use lispa\amos\core\user\User;
use lispa\amos\notificationmanager\AmosNotify;
use lispa\amos\notificationmanager\models\Notification;
use lispa\amos\notificationmanager\record\NotifyRecord;
use Yii;

/**
 * Class ContentMailBuilder
 * @package lispa\amos\notificationmanager\base\builder
 */
class ContentMailBuilder extends AMailBuilder
{
    /**
     * @return string
     */
    public function getSubject(array $resultset)
    {
        return Yii::t('amosnotify', "#Content_Change_Subject_Notify");
    }

    /**
     * @inheritdoc
     */
    public function renderEmail(array $resultset, User $user)
    {
        $mail = '';
        $class_content = '';
        try {
            $mail .= $this->renderContentHeader($resultset);
            foreach ($resultset as $notify) {
                /** @var Notification $notify */
                $cls_name = $notify->class_name;
                /** @var NotifyRecord|ModelLabelsInterface $model */
                $model = $cls_name::find()->andWhere(['id' => $notify->content_id])->one();
                if (!is_null($model) && $model->sendCommunication()) {
                    if (strcmp($class_content, $notify->class_name)) {
                        $mail .= $this->renderContentTitle($model);
                        $class_content = $notify->class_name;
                    }
                    $mail .= $this->renderContent($model, $user);
                }
            }
            $mail .= $this->renderContentFooter($resultset, $user);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $mail;
    }

    /**
     * @param Record $model
     * @param User $user
     * @return string
     */
    protected function renderContent(Record $model, $user)
    {
        $controller = \Yii::$app->controller;
        $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/content", [
            'model' => $model,
            'profile' => $user->userProfile
        ]);
        return $ris;
    }

    /**
     * @param ModelLabelsInterface $model
     * @return string
     */
    protected function renderContentTitle(ModelLabelsInterface $model)
    {
        $controller = \Yii::$app->controller;
        $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_title", [
            'title' => $model->getGrammar()->getModelLabel(),
        ]);
        return $ris;
    }

    /**
     * @param array $resultset
     * @return string
     */
    protected function renderContentHeader(array $resultset)
    {
        $controller = \Yii::$app->controller;
        $contents_number = count($resultset);
        $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_header", [
            'contents_number' => $contents_number
        ]);
        return $ris;
    }

    /**
     * @param array $resultset
     * @return string
     */
    protected function renderContentFooter(array $resultset, User $user)
    {
        $controller = \Yii::$app->controller;
        $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_footer", ['user' => $user]);
        return $ris;
    }
}
