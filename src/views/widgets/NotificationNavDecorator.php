<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\notify
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

namespace open20\amos\notificationmanager\views\widgets;

use yii\bootstrap\Widget;
use yii\bootstrap\Nav;
use yii\base\InvalidConfigException;
use Yii;
use yii\helpers\Html;
use yii\db\Query;

class NotificationNavDecorator extends Widget {

    /**
     *
     * @var Nav 
     */
    protected $nav;
    protected $position = 0;
    public $icon = 'glyphicon glyphicon-bell';

    public function init() {
        parent::init();

        if (empty($this->nav) === true) {
            throw new InvalidConfigException('Nav property must be provided');
        }

        if ($this->position > count($this->nav->items) - 1) {
            throw new InvalidConfigException('Position must be in range with items');
        }

        $this->decorate();
    }

    protected function decorate() {
        $item = $this->createItem();
        array_splice($this->nav->items, $this->position, 0, [$item]);
    }

    protected function createItem() {
        $nQuery = (new Query())->from(Notification::tableName())->where(['user_id' => Yii::$app->user->id]);
        $nCount = $nQuery->count();
        $notifications = $nQuery->select(['text', 'n' => 'COUNT(*)'])->groupBy(['path_info'])->all();
        $label = Html::tag('i', '', ['class' => $this->icon]);
        $item['label'] = &$label;
        if ($nCount > 0) {
            $label .= Html::tag('span', $nCount, ['class' => 'badge']);
            $items = [];

            foreach ($notifications as $notification) {
                $items[] = ['label' => Yii::t('notifications', $notification['text'], [
                        'count' => $notification['n']
                    ]), 'url' => $notification['path_info']];
            }
            $item['items'] = $items;
        }
        return $item;
    }

    public function setNav(Nav $nav) {
        $this->nav = $nav;
    }

    public function setPosition($pos) {
        if (is_numeric($pos) === false) {
            throw new InvalidConfigException('Position must be a numeric value');
        }
        $this->position = $pos;
    }

}
