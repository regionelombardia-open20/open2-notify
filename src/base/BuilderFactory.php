<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\notify
 * @category   CategoryName
 */

namespace open20\amos\notificationmanager\base;

use open20\amos\notificationmanager\base\builder\ContentImmediateMailBuilder;
use open20\amos\notificationmanager\base\builder\ContentMailBuilder;
use open20\amos\notificationmanager\base\builder\CustomMailBuilder;
use open20\amos\notificationmanager\base\builder\ValidatedMailBuilder;
use open20\amos\notificationmanager\base\builder\ValidatorsMailBuilder;
use open20\amos\notificationmanager\models\ChangeStatusEmail;
use yii\base\BaseObject;

class BuilderFactory extends BaseObject {

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
