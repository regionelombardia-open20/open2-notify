<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\views\email
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

use open20\amos\notificationmanager\AmosNotify;

/**
 * @var integer $contents_number
 */
$notifyModule = AmosNotify::instance();
?>

<div style="box-sizing:border-box;color:#000000;">
    <div style="padding:5px 10px;background-color: #F2F2F2;text-align:center;">
	    <h1 style="color:<?= $notifyModule->mailThemeColor['bgPrimary'] ?>;font-size:1.2em;margin:0;">
            <?= AmosNotify::t('amosnotify', '#Platform_update') ?>
        </h1>
        <p style="font-size:1em;margin:0;margin-top:5px;">
            <?php
            if ($contents_number == 1):
                ?>
                <?= AmosNotify::t('amosnotify', '#There_is_content_interest', [$contents_number]) ?>
                <?php
            else:
                ?>
                <?= AmosNotify::t('amosnotify', '#There_is_content_interest_plural', [$contents_number]) ?>
            <?php
            endif;
            ?>
        </p>
    </div>
</div>