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
use lispa\amos\notificationmanager\models\ChangeStatusEmail;
use Yii;
use yii\base\Exception;

/**
 * Class CustomMailBuilder
 * @package lispa\amos\notificationmanager\base\builder
 */
class CustomMailBuilder extends AMailBuilder
{

    /**
     * @var ChangeStatusEmail $emailConf
     */
    public $emailConf;

    /**
     * @var string $endStatus
     */
    public $endStatus;

    /**
     * @var string $template
     */
    private $template;

    /**
     * @var array $params
     */
    private $params = [];

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->init();
    }

    public function init()
    {
        if(!is_null($this->emailConf)){
            $email = $this->emailConf;
            if($email->template){
                $this->setTemplate($email->template);
            } elseif($email->toCreator){
                $this->setTemplate("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/validated");
            } else {
                $this->setTemplate("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/validator");
            }
            if(count($email->params)){
                $this->setParams($email->params);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getSubject(array $resultset)
    {
        $stdMsg = AmosNotify::t('amosnotify', "Content has been validated");
        $model = reset($resultset);
        //check if customized subject
        if(!is_null($this->emailConf) && !is_null($this->emailConf->subject)){
            $stdMsg = $this->emailConf->subject;
        } elseif ($model instanceof ModelLabelsInterface) {
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

            $userid = $model->getStatusLastUpdateUser($this->endStatus);
            if (!is_null($userid)) {
                $user = User::findOne($userid);
                $comment = $model->getStatusLastUpdateComment($this->endStatus);
                $controller = \Yii::$app->controller;

                if(!count($this->getParams())){
                    $this->setParams([
                        'model' => $model,
                        'validator' => $user->getUserProfile()->one(),
                        'comment' => $comment
                    ]);
                }
                $ris = $controller->renderPartial($this->getTemplate(), $this->getParams());
            }
        } catch (\Exception $ex) {
            Yii::getLogger()->log($ex->getMessage(), \yii\log\Logger::LEVEL_ERROR);
        }

        return $ris;
    }
}
