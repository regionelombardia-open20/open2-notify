<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    piattaforma-openinnovation
 * @category   CategoryName
 */

namespace lispa\amos\notificationmanager\forms;

use lispa\amos\notificationmanager\record\NotifyRecord;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Class NewsWidget
 * @package lispa\amos\notificationmanager\forms
 */
class NewsWidget extends Widget
{
    public $model;
    public $style;
    public $css_class = 'new-badge badge';

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->model instanceof NotifyRecord) {
            if ($this->model->isNews()) {
                echo Html::beginTag('div', ['class' => $this->css_class, 'style' => $this->style]);
                echo \Yii::t("amosnotify", "NEW");
                echo Html::endTag('div');
            }
        }
    }
}
