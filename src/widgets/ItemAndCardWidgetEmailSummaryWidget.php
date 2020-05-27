<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
/**
 */

namespace open20\amos\notificationmanager\widgets;


use open20\amos\core\forms\ItemAndCardHeaderWidget;
use yii\base\Widget;


class ItemAndCardWidgetEmailSummaryWidget extends Widget
{
    public $model;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return ItemAndCardHeaderWidget::widget([
            'model' => $this->model,
            'layout' => '@vendor/open20/amos-notify/src/views/email/item_and_card_header_widget_mail.php',
            'publicationDateNotPresent' => true,
            'showPrevalentPartnershipAndTargets' => true,
            'absoluteUrlAvatar' => true,
        ]);
    }

}