<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos-notify
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
 * @var $comment
 */

$notifyModule = AmosNotify::instance();
$contextClassname = $comment->context;
$modelContext = $contextClassname::findOne($comment->context_id);
$viewUrl = '';
if($modelContext) {
   $viewUrl =  Yii::$app->urlManager->createAbsoluteUrl($modelContext->getFullViewUrl());
}
?>

<tr>
    <td style="padding:0">
        <table width="100%">
            <tbody><tr>
                <td width="400" style="text-align:left">
                    <table width="100%">
                        <tbody><tr>
                            <td colspan="2" style="text-align:left">
                                <strong style="font-family:sans-serif;font-size:11px;color:#000"><?= $comment->createdUserProfile->nomeCognome?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:left">
                                <em style="font-family:sans-serif;font-size:14px;color:#000">"<?= Yii::$app->getFormatter()->asRaw(strip_tags($comment->comment_text)) ?>"</em>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td align="right" width="85" height="20" valign="bottom" style="text-align:center;padding-left:10px">
                    <a style="background: <?= $notifyModule->mailThemeColor['bgPrimary'] ?>; border:3px solid <?= $notifyModule->mailThemeColor['bgPrimary'] ?>; color: <?= $notifyModule->mailThemeColor['textContrastBgPrimary'] ?>; font-family: sans-serif; font-size: 11px; line-height: 22px; text-align: center; text-decoration: none; display: block; font-weight: bold; text-transform: uppercase; height: 20px;"
                            class="button-a" href="<?= $viewUrl ?>">
                        <?= AmosNotify::t('amosnotify', "Rispondi")?>
                    </a>
                </td>
            </tr>

            </tbody>
        </table>
    </td>
</tr>