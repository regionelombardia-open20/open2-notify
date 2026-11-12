<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\base
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

namespace open20\amos\notificationmanager\base;

use open20\amos\notificationmanager\base\builder\BookmarksValidatorsMailBuilder;
use open20\amos\notificationmanager\base\builder\ContactAcceptedMailBuilder;
use open20\amos\notificationmanager\base\builder\ContentImmediateMailBuilder;
use open20\amos\notificationmanager\base\builder\ContentMailBuilder;
use open20\amos\notificationmanager\base\builder\CustomMailBuilder;
use open20\amos\notificationmanager\base\builder\CachedContentMailBuilder;
use open20\amos\notificationmanager\base\builder\NewsletterBuilder;
use open20\amos\notificationmanager\base\builder\SleepingUserMailBuilder;
use open20\amos\notificationmanager\base\builder\SuccessfulContentMailBuilder;
use open20\amos\notificationmanager\base\builder\SuccessfulUserMailBuilder;
use open20\amos\notificationmanager\base\builder\SuggestedLinkMailBuilder;
use open20\amos\notificationmanager\base\builder\ValidatedMailBuilder;
use open20\amos\notificationmanager\base\builder\ValidatorsMailBuilder;
use open20\amos\notificationmanager\models\ChangeStatusEmail;
use yii\base\BaseObject;

/**
 * Class BuilderFactory
 * @package open20\amos\notificationmanager\base
 */
class BuilderFactory extends BaseObject
{
    const CONTENT_MAIL_BUILDER = 1;
    const CONTENT_MAIL_CACHED_BUILDER = 12;
    const VALIDATORS_MAIL_BUILDER = 2;
    const VALIDATED_MAIL_BUILDER = 3;
    const CUSTOM_MAIL_BUILDER = 4;
    const CONTENT_IMMEDIATE_MAIL_BUILDER = 5;
    const CONTENT_SLEEPING_USER_BUILDER = 6;
    const CONTENT_SUCCESSFUL_CONTENT_BUILDER = 7;
    const CONTENT_SUCCESSFUL_USER_BUILDER = 8;
    const CONTENT_SUGGESTED_LINK_BUILDER = 9;
    const CONTENT_CONTACT_ACCEPTED_BUILDER = 10;
    const NEWSLETTER_BUILDER = 11;
    const BOOKMARKS_VALIDATORS_MAIL_BUILDER = 12;
    
    /**
     * @param int $type
     * @param ChangeStatusEmail|null $email
     * @param string|null $endStatus
     * @return ContentMailBuilder|CustomMailBuilder|ValidatedMailBuilder|ValidatorsMailBuilder|null
     */
    public function create($type, $email = null, $endStatus = null)
    {
        $obj = null;
        
        switch ($type) {
            case self::CONTENT_MAIL_BUILDER:
                $obj = new ContentMailBuilder();
                break;
            case self::CONTENT_MAIL_CACHED_BUILDER:
                $obj = new CachedContentMailBuilder();
                break;
            case self::CONTENT_IMMEDIATE_MAIL_BUILDER:
                $obj = new ContentImmediateMailBuilder();
                break;
            case self::VALIDATORS_MAIL_BUILDER:
                $obj = new ValidatorsMailBuilder();
                break;
            case self::VALIDATED_MAIL_BUILDER:
                $obj = new ValidatedMailBuilder();
                break;
            case self::CUSTOM_MAIL_BUILDER:
                $obj = new CustomMailBuilder(['emailConf' => $email, 'endStatus' => $endStatus]);
                break;
            case self::CONTENT_SLEEPING_USER_BUILDER:
                $obj = new SleepingUserMailBuilder();
                break;
            case self::CONTENT_SUCCESSFUL_CONTENT_BUILDER:
                $obj = new SuccessfulContentMailBuilder();
                break;
            case self::CONTENT_SUCCESSFUL_USER_BUILDER:
                $obj = new SuccessfulUserMailBuilder();
                break;
            case self::CONTENT_SUGGESTED_LINK_BUILDER:
                $obj = new SuggestedLinkMailBuilder();
                break;
            case self::CONTENT_CONTACT_ACCEPTED_BUILDER:
                $obj = new ContactAcceptedMailBuilder();
                break;
            case self::NEWSLETTER_BUILDER:
                $obj = new NewsletterBuilder();
                break;
            case self::BOOKMARKS_VALIDATORS_MAIL_BUILDER:
                $obj = new BookmarksValidatorsMailBuilder();
                break;
        }
        
        return $obj;
    }
}
