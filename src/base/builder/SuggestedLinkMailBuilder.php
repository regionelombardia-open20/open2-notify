<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\base\builder
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

namespace open20\amos\notificationmanager\base\builder;

use Yii;

/**
 * Class SleepingUserMailBuilder
 * @package open20\amos\notificationmanager\base\builder
 */
class SuggestedLinkMailBuilder extends ContentMailBuilder
{
    /**
     * @return string
     */
    public function getSubject(array $resultset)
    {
        return Yii::t('amosnotify', "#Suggested_link_Subject");
    }
    
    
    /**
     * @param $resultSetNormal
     * @param $user
     * @return string
     */
    protected function renderEmailUserNotify($resultSetNormal, $user){
        $mail = '';
//        try {
            if (isset($resultSetNormal['community']) && count($resultSetNormal['community'])) {
                // ------------ USER IN COMMUNITY  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_community_title"), Yii::t('amosnotify', "#SuggestedLink_community_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['community'], $user);
            }
            
            if (isset($resultSetNormal['comments']) && count($resultSetNormal['comments'])) {
                // ------------ WHO WROTE COMMENTS  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_comments_title"), Yii::t('amosnotify', "#SuggestedLink_comments_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['comments'], $user);
            }
            
            if (isset($resultSetNormal['network']) && count($resultSetNormal['network'])) {
                // ------------ CONTACTS OF CONTACTS  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_network_title"), Yii::t('amosnotify', "#SuggestedLink_network_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['network'], $user);
            }
            
            if (isset($resultSetNormal['organizations']) && count($resultSetNormal['organizations'])) {
                // ------------ IN THE SAME ORGANIZZATIONS  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_organizations_title"), Yii::t('amosnotify', "#SuggestedLink_organizations_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['organizations'], $user);
            }
            

//        } catch (\Exception $ex) {
//            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
//        }

        return $mail;
    }
}