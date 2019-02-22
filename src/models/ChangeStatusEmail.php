<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\notificationmanager\models
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\models;


use yii\base\Object;

/**
 * Class ChangeStatusEmail
 *
 * Model used to create a custom email on change workflow status
 *
 * @package lispa\amos\notificationmanager\models
 */
class ChangeStatusEmail extends Object
{
    /**
     * @var string $startStatus
     */
    public $startStatus;

    /**
     * @var string $template
     */
    public $template;

    /**
     * @var string $subject
     */
    public $subject;

    /**
     * @var array $params
     */
    public $params = [];

    /**
     * @var bool|false $toCreator
     */
    public $toCreator = false;

    /**
     * @var bool|true $toValidator
     */
    public $toValidator = true;

    /**
     * @var array $recipientUserIds
     */
    public $recipientUserIds = [];

}