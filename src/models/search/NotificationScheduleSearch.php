<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notificationmanager\models\search
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

namespace open20\amos\notificationmanager\models\search;

use open20\amos\core\interfaces\NewsletterInterface;
use open20\amos\core\record\Record;
use open20\amos\core\user\User;
use open20\amos\notificationmanager\AmosNotify;
use open20\amos\notificationmanager\exceptions\NewsletterException;
use open20\amos\notificationmanager\models\Newsletter;
use open20\amos\notificationmanager\models\NotificationSchedule;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Class NewsletterSearch
 * NewsletterSearch represents the model behind the search form about `backend\amos\notify\models\Newsletter`.
 * @package open20\amos\notificationmanager\models\search
 */
class NotificationScheduleSearch extends NotificationSchedule
{
    public $status_schedule;
    public $content_id;
    public $send_date;
    public $plugin;

    public $user_id;
    public $notification_id;
    public $nome;
    public $cognome;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome', 'cognome','email','user_id', 'notification_id', 'status_schedule', 'content_id', 'send_date', 'plugin'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * This is the base search.
     * @param array $params
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function baseSearch($params)
    {
        $query = NotificationSchedule::find()
            ->leftJoin('notification_schedule_content', 'notification_schedule.id = notification_schedule_id')
            ->leftJoin('notification', 'notification.id = notification_schedule_content.notification_id')
            ->orderBy('notification_schedule.id DESC')
            ->groupBy('notification_schedule.id');


        return $query;
    }


    /**
     * Base filter.
     * @param ActiveQuery $query
     * @return mixed
     */
    public function baseFilter($query)
    {

        $query->andFilterWhere(['notification.content_id' => $this->content_id]);
        $query->andFilterWhere(['notification_schedule_content.classname' => $this->plugin]);
        $query->andFilterWhere(['notification_schedule.status' => $this->status_schedule]);
        if (!empty($this->send_date)) {
            $query->andWhere(["DATE_FORMAT(notification_schedule.created_at, '%Y-%m-%d')" => $this->send_date]);
        }
//        pr($query->createCommand()->rawSql);
        return $query;
    }

    public function search($params)
    {
        $query = $this->baseSearch($params);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
//        $this->setSearchSort($dataProvider);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $this->baseFilter($query);
//        pr($query->createCommand()->rawSql);die;
        return $dataProvider;
    }


    public function searchUsers($params)
    {
        $query = User::find()
            ->innerjoin('user_profile', 'user.id=user_profile.user_id')
            ->innerJoin('notificationread', 'user.id = notificationread.user_id')
            ->innerJoin('notification_schedule_content', 'notification_schedule_content.notification_id =notificationread.notification_id ')
            ->andWhere(['notification_schedule_content.notification_id' => $this->notification_id])
            ->andWhere(['notificationread.notification_id' => $this->notification_id])
            ->andWhere(['notificationread.notification_type' => 2]);

        $this->load($params);
//        $query->andFilterWhere(['user.id' => $this->user_id]);
        $query->andFilterWhere(['like','user_profile.nome' , $this->nome]);
        $query->andFilterWhere(['like','user_profile.cognome', $this->cognome]);
        $query->andFilterWhere(['like','user_profile.email', $this->email]);
//        $query->andFilterWhere(['user_profile.nome' => $this->user_id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
//        pr($query->createCommand()->rawSql); die;
        return $dataProvider;
    }

}
