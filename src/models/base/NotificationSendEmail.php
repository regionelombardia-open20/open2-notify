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

namespace open20\amos\notificationmanager\models\base;

use open20\amos\core\record\Record;
use Yii;

/**
 * This is the base-model class for table "notification_send_email".
 *
 * @property integer $id
 * @property string $classname
 * @property integer $content_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  NotificationSendEmail extends Record
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification_send_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['classname', 'content_id'], 'required'],
            [['content_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['classname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosnotification', 'ID'),
            'classname' => Yii::t('amosnotification', 'Classname'),
            'content_id' => Yii::t('amosnotification', 'Content ID'),
            'created_at' => Yii::t('amosnotification', 'Creato il'),
            'updated_at' => Yii::t('amosnotification', 'Aggiornato il'),
            'deleted_at' => Yii::t('amosnotification', 'Cancellato il'),
            'created_by' => Yii::t('amosnotification', 'Creato da'),
            'updated_by' => Yii::t('amosnotification', 'Aggiornato da'),
            'deleted_by' => Yii::t('amosnotification', 'Cancellato da'),
        ];
    }
}
