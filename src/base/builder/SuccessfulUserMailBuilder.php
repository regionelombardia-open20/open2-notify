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

use open20\amos\notificationmanager\AmosNotify;
use Yii;

/**
 * Class SleepingUserMailBuilder
 * @package open20\amos\notificationmanager\base\builder
 */
class SuccessfulUserMailBuilder extends SuccessfulContentMailBuilder
{
    /**
     * @return string
     */
    public function getSubject(array $resultset)
    {
        return Yii::t('amosnotify', "#Successful_User_Subject");
    }
    
    /**
     * @param $resultSetNormal
     * @param $user
     * @return string
     */
    protected function renderEmailUserNotify($resultSetNormal, $user){
        $mail = '';
//        try {
            if (isset($resultSetNormal['view_profile']) && count($resultSetNormal['view_profile'])) {
                
                $frequencyMsg = AmosNotify::t('amosnotify', $this->getFrequencyMessage());
                $viewMsg =  AmosNotify::t('amosnotify', '#Visto da ##n## persone', [$resultSetNormal['view_profile_howmany']]);
                $howManyMsg =  AmosNotify::t('amosnotify', '#Profilo ##visto## ##frequenza##', [$viewMsg, $frequencyMsg]);
                
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_view_profile_quanti_title"), $howManyMsg);
                
                // ------------ HANNO VISTO IL SUO PROFILO  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#SuggestedLink_view_profile_title"), Yii::t('amosnotify', "#SuggestedLink_view_profile_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['view_profile'], $user);
            }
                        

//        } catch (\Exception $ex) {
//            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
//        }

        return $mail;
    }
        
}