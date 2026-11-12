<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\models\base
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

namespace open20\amos\notificationmanager\models\base;

use open20\amos\core\record\Record;
use open20\amos\notificationmanager\AmosNotify;

/**
 * Class Newsletter
 *
 * This is the base-model class for table "notification_newsletter".
 *
 * @property integer $id
 * @property string $status
 * @property string $subject
 * @property string $send_date_begin
 * @property string $send_date_end
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\notificationmanager\models\NewsletterContents[] $newsletterContents
 *
 * @package open20\amos\notificationmanager\models\base
 */
abstract class Newsletter extends Record
{
    /**
     * @var AmosNotify $notifyModule
     */
    protected $notifyModule = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->notifyModule = AmosNotify::instance();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_newsletter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'subject'], 'required'],
            [['send_date_begin', 'send_date_end', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['status', 'subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosNotify::t('amosnotify', 'ID'),
            'status' => AmosNotify::t('amosnotify', '#newsletter_status'),
            'subject' => AmosNotify::t('amosnotify', '#newsletter_subject'),
            'send_date_begin' => AmosNotify::t('amosnotify', '#newsletter_send_date_begin'),
            'send_date_end' => AmosNotify::t('amosnotify', '#newsletter_send_date_end'),
            'created_at' => AmosNotify::t('amosnotify', '#creation_date'),
            'updated_at' => AmosNotify::t('amosnotify', 'Updated at'),
            'deleted_at' => AmosNotify::t('amosnotify', 'Deleted at'),
            'created_by' => AmosNotify::t('amosnotify', 'Created by'),
            'updated_by' => AmosNotify::t('amosnotify', 'Updated by'),
            'deleted_by' => AmosNotify::t('amosnotify', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletterContents()
    {
        return $this->hasMany($this->notifyModule->model('NewsletterContents'), ['newsletter_id' => 'id']);
    }
}
