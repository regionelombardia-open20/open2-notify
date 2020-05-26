<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notificationmanager\base\builder
 * @category   CategoryName
 */

namespace open20\amos\notificationmanager\base\builder;

use open20\amos\core\interfaces\ModelLabelsInterface;
use open20\amos\core\models\ModelsClassname;
use open20\amos\core\record\Record;
use open20\amos\core\user\User;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\models\Notification;
use open20\amos\notificationmanager\record\NotifyRecord;
use Yii;
use yii\helpers\Console;

/**
 * Class ContentMailBuilder
 * @package open20\amos\notificationmanager\base\builder
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
                $modelClassname = ModelsClassname::find()->andWhere(['classname' => $cls_name])->one();

                /** @var NotifyRecord|ModelLabelsInterface $model */
                $model = $cls_name::find()->andWhere(['id' => $notify->content_id])->one();
                if (!is_null($model) && $model->sendCommunication()) {
                    if (strcmp($class_content, $notify->class_name)) {
                        $mail .= $this->renderContentTitle($model);
                        $class_content = $notify->class_name;

                        if($modelClassname) {
                            $module = \Yii::$app->getModule($modelClassname->module);
                            if (!empty($module->viewPathEmailContentSubtitle[$cls_name])) {
                                $mail .= $this->renderPersonalizedContentSubtitle($model, $user, $module->viewPathEmailContentSubtitle[$cls_name]);
                            }
                        }
                    }
                    // render list of content of default
                    $mail .= $this->renderContent($model, $user);
                }
            }
            $mail .= $this->renderContentFooter($resultset, $user);
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
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
        $ris = $controller->renderPartial("@vendor/open20/amos-" . AmosNotify::getModuleName() . "/src/views/email/content", [
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
        $ris = $controller->renderPartial("@vendor/open20/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_title", [
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
        $ris = $controller->renderPartial("@vendor/open20/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_header", [
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
        $ris = $controller->renderPartial("@vendor/open20/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_footer", ['user' => $user]);
        return $ris;
    }

    /**
     * @param Record[] $model
     * @param User $user
     * @return string
     */
    protected function renderPersonalizedContentSubtitle($model, $user, $viewPath)
    {
        Console::stdout($user->userProfile->id. PHP_EOL);

        $controller = \Yii::$app->controller;
        $ris = $controller->renderPartial($viewPath, [
            'model' => $model,
            'profile' => $user->userProfile,
        ]);
        return $ris;
    }
}
