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
 * Class NewsletterContents
 *
 * This is the base-model class for table "notification_newsletter_contents".
 *
 * @property integer $id
 * @property integer $newsletter_id
 * @property integer $newsletter_contents_conf_id
 * @property integer $content_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\notificationmanager\models\Newsletter $newsletter
 * @property \open20\amos\notificationmanager\models\NewsletterContentsConf $newsletterContentsConf
 * @property \open20\amos\notificationmanager\models\NewsletterContents[] $newsletterContentWithBrothers
 *
 * @package open20\amos\notificationmanager\models\base
 */
abstract class NewsletterContents extends Record
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
        return 'notification_newsletter_contents';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['newsletter_id', 'newsletter_contents_conf_id', 'content_id'], 'required'],
            [['newsletter_id', 'newsletter_contents_conf_id', 'content_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['newsletter_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this->notifyModule->model('Newsletter'), 'targetAttribute' => ['newsletter_id' => 'id']],
            [['newsletter_contents_conf_id'], 'exist', 'skipOnError' => true, 'targetClass' => $this->notifyModule->model('NewsletterContentsConf'), 'targetAttribute' => ['newsletter_contents_conf_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => AmosNotify::t('amosnotify', 'ID'),
            'newsletter_id' => AmosNotify::t('amosnotify', 'Newsletter Id'),
            'newsletter_contents_conf_id' => AmosNotify::t('amosnotify', 'Newsletter Contents Conf Id'),
            'content_id' => AmosNotify::t('amosnotify', 'Content ID'),
            'created_at' => AmosNotify::t('amosnotify', 'Created at'),
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
    public function getNewsletter()
    {
        return $this->hasOne($this->notifyModule->model('Newsletter'), ['id' => 'newsletter_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletterContentsConf()
    {
        return $this->hasOne($this->notifyModule->model('NewsletterContentsConf'), ['id' => 'newsletter_contents_conf_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNewsletterContentWithBrothers()
    {
        return $this->hasMany(self::className(), ['newsletter_id' => 'newsletter_id'])
            ->andWhere(['newsletter_contents_conf_id' => $this->newsletter_contents_conf_id]);
    }
}
