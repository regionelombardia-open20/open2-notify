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

use lispa\amos\core\user\User;
use lispa\amos\core\utilities\Email;
use lispa\amos\notificationmanager\base\Builder;
use Yii;
use yii\base\Object;

/**
 * Class AMailBuilder
 * @package lispa\amos\notificationmanager\base\builder
 */
abstract class AMailBuilder extends Object implements Builder
{
    /**
     * @param array $userIds
     * @param array $resultset
     * @return bool
     */
    public function sendEmail(array $userIds, array $resultset)
    {
        $allOk = true;
        try {
            foreach ($userIds as $id) {
                $user = User::findOne($id);
                if (!is_null($user)) {
                    $this->setUserLanguage($id);
                    $subject = $this->getSubject($resultset);
                    $message = $this->renderEmail($resultset, $user);
                    $email = new Email();
                    $from = '';
                    if (isset(Yii::$app->params['email-assistenza'])) {
                        // Use default platform email assistance
                        $from = Yii::$app->params['email-assistenza'];
                    }
                    $ok = $email->sendMail($from, [$user->email], $subject, $message);
                    if (!$ok) {
                        Yii::getLogger()->log("Errore invio mail da '$from' a '$user->email'", \yii\log\Logger::LEVEL_ERROR);
                        $allOk = false;
                    }
                }
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
            $allOk = false;
        }
        return $allOk;
    }

    /**
     * @param int $userId
     */
    protected function setUserLanguage($userId)
    {
        $module = \Yii::$app->getModule('translation');
        if ($module && !empty($module->enableUserLanguage) && $module->enableUserLanguage == true) {
            /** @var \lispa\amos\translation\AmosTranslation $module */
            $lang = $module->getUserLanguage($userId);
            $module->setAppLanguage($lang);
        }
    }
}
