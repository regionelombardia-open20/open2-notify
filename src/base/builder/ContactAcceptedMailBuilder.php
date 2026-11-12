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
class ContactAcceptedMailBuilder extends ContentMailBuilder
{
    //protected $results;
    protected $section;
    protected $section_title;

    /**
     * @return string
     */
    public function getSubject(array $resultset)
    {
        return Yii::t('amosnotify', "#Contact_accepted_Subject");
    }

    
    /**
     * @param $resultSetNormal
     * @param $user
     * @return string
     */
    protected function renderEmailUserNotify($resultSetNormal, $user){
        $mail = '';
        //$this->results = $resultSetNormal;
//        try {
            if (isset($resultSetNormal['created_by']) && count($resultSetNormal['created_by'])) {
                // ------------ NOTIFICATION SECTION CREATED BY  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#Contact_accepted_Created_by_title"), Yii::t('amosnotify', "#Contact_accepted_Created_by_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['created_by'], $user);
            }
            
            if (isset($resultSetNormal['commented_by']) && count($resultSetNormal['commented_by'])) {
                // ------------ NOTIFICATION SECTION CREATED BY  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#Contact_accepted_Commented_by_title"), Yii::t('amosnotify', "#Contact_accepted_Commented_by_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['commented_by'], $user);
            }
            
            if (isset($resultSetNormal['liked_by']) && count($resultSetNormal['liked_by'])) {
                // ------------ NOTIFICATION SECTION CREATED BY  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#Contact_accepted_Liked_by_title"), Yii::t('amosnotify', "#Contact_accepted_Liked_by_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['liked_by'], $user);
            }
            
            if (isset($resultSetNormal['connected_to']) && count($resultSetNormal['connected_to'])) {
                // ------------ NOTIFICATION SECTION CREATED BY  -------------
                $mail .= $this->renderSectionTitle(Yii::t('amosnotify', "#Contact_accepted_Connected_to_title"), Yii::t('amosnotify', "#Contact_accepted_Connected_to_desc"));        

                $mail .= $this->renderSectionWithClasses($resultSetNormal['connected_to'], $user);
            }
            

//        } catch (\Exception $ex) {
//            Yii::getLogger()->log($ex->getTraceAsString(), \yii\log\Logger::LEVEL_ERROR);
//        }

        return $mail;
    }

}