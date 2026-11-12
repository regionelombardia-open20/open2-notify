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

$colors =  \open20\amos\notificationmanager\utility\NotifyUtility::getColorNetwork($color);
$urlIcon = \Yii::$app->params['platform']['frontendUrl'].\open20\amos\notificationmanager\utility\NotifyUtility::getIconNetwork($color);

?>

<tr>
    <td colspan="2" style="padding-top:15px;" width="100%">
        <table cellspacing="0" cellpadding="0" border="0" align="center" class="email-container" width="100%" style="width:100%">
            <tr>
                <td bgcolor=" <?= $colors[0] ?>" align="center" style="width:40px; padding:5px"><img src="<?= $urlIcon ?>" height="20" border="0" align="center"></td>
                <td bgcolor=" <?= $colors[1] ?>" style="font-family:sans-serif; color:#FFF; font-weight:bold; font-size:16px; padding:5px 10px; width:520px">
                    <p style="margin:8px 0;"><?= $modelNetwork->getTitle() ?></p>
                </td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td colspan="2" style="padding-bottom:10px;">
        <table cellspacing="0" cellpadding="0" border="0" align="center"   class="email-container" width="100%">
            <tr>
                <td bgcolor="#FFFFFF" style="padding:10px 15px 10px 15px; border-left:5px solid <?= $colors[2] ?>">
                    <table width="100%">