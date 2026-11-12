<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\widgets
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\notificationmanager\widgets;

use open20\amos\core\forms\ItemAndCardHeaderWidget;

/**
 * Class ItemAndCardWidgetEmailSummaryWidget
 * @package open20\amos\notificationmanager\widgets
 */
class ItemAndCardWidgetEmailSummaryWidget extends ItemAndCardHeaderWidget
{
    /**
     * @var string $layout Widget view
     */
    public $layout = "@vendor/open20/amos-notify/src/views/email/item_and_card_header_widget_mail.php";
    
    /**
     * @var bool $showPrevalentPartnershipAndTargets
     */
    public $showPrevalentPartnershipAndTargets = false;
    
    /**
     * @var bool $absoluteUrlAvatar
     */
    public $absoluteUrlAvatar = true;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setPublicationDateNotPresent(true);
        parent::init();
    }
}
