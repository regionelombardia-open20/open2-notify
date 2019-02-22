<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notify
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\base;

use lispa\amos\notificationmanager\base\builder\ContentImmediateMailBuilder;
use lispa\amos\notificationmanager\base\builder\ContentMailBuilder;
use lispa\amos\notificationmanager\base\builder\CustomMailBuilder;
use lispa\amos\notificationmanager\base\builder\ValidatedMailBuilder;
use lispa\amos\notificationmanager\base\builder\ValidatorsMailBuilder;
use lispa\amos\notificationmanager\models\ChangeStatusEmail;
use yii\base\Object;

class BuilderFactory extends Object {

    const CONTENT_MAIL_BUILDER = 1;
    const VALIDATORS_MAIL_BUILDER = 2;
    const VALIDATED_MAIL_BUILDER = 3;
    const CUSTOM_MAIL_BUILDER = 4;
    const CONTENT_IMMEDIATE_MAIL_BUILDER = 5;

    /**
     * @param $type
     * @param ChangeStatusEmail|null $email
     * @param string|null $endStatus
     * @return ContentMailBuilder|CustomMailBuilder|ValidatedMailBuilder|ValidatorsMailBuilder|null
     */
    public function create($type, $email = null, $endStatus = null){
        $obj = null;

        switch ($type){
            case self::CONTENT_MAIL_BUILDER:
                $obj = new ContentMailBuilder();
                break;
            case self::CONTENT_IMMEDIATE_MAIL_BUILDER:
                $obj = new ContentImmediateMailBuilder();
                break;
            case self::VALIDATORS_MAIL_BUILDER:
                $obj = new ValidatorsMailBuilder();
                break;
            case self::VALIDATED_MAIL_BUILDER:
                $obj = new ValidatedMailBuilder();
                break;
            case self::CUSTOM_MAIL_BUILDER:
                $obj = new CustomMailBuilder(['emailConf' => $email, 'endStatus' => $endStatus]);
                break;
        }

        return $obj;
    }
}
