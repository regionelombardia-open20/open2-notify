<?php
/**
 * Created by PhpStorm.
 * User: michele.lafrancesca
 * Date: 28/08/2019
 * Time: 16:43
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
            'showPrevalentPartnershipAndTargets' => false,
            'absoluteUrlAvatar' => true,
        ]);
    }

}