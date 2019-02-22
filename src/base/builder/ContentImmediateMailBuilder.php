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

use lispa\amos\notificationmanager\AmosNotify;

class ContentImmediateMailBuilder extends ContentMailBuilder
{

    /**
     * @param array $resultset
     * @return string
     */
    protected function renderContentHeader(array $resultset)
    {
        $controller = \Yii::$app->controller;
        $contents_number = count($resultset);
        $ris = $controller->renderPartial("@vendor/lispa/amos-" . AmosNotify::getModuleName() . "/src/views/email/content_immediate_email_header", [
            'contents_number' => $contents_number
        ]);
        return $ris;
    }

}