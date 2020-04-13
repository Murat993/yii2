<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 03.08.18
 * Time: 14:51
 */

namespace common\behaviors;


use common\models\Changelog;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use Yii;

class ChangelogBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete'
        ];
    }


    public function beforeUpdate($event)
    {
        $this->diffAttributes($this->owner->getOldAttributes(), $this->owner->attributes);
    }

    public function afterInsert($event)
    {
        $this->logAction(Changelog::TYPE_CREATE);
    }

    public function beforeDelete($event)
    {
        $this->logAction(Changelog::TYPE_DELETE);
    }

    function diffAttributes($old, $current)
    {
        $diffKeys = array_keys(array_diff($old, $current));
        if (Yii::$app instanceof \yii\console\Application) {
            $userId = 9999999;
        } else {
            $userId = Yii::$app->user ? Yii::$app->user->id : 9999999;
        }
        $zone = new \DateTimeZone('Asia/Almaty');
        $currentDate = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
        foreach ($diffKeys as $key) {
            $log = new Changelog();
            $log->entity_id = $this->owner->id;
            $log->new_value = $this->owner->$key;
            $log->old_value = $this->owner->getOldAttribute($key);
            $log->user_id = $userId;
            $log->tablename = $this->owner->tableName();
            $log->datetime = $currentDate;
            $log->field_name = $key;
            $log->type = Changelog::TYPE_UPDATE;
            $log->save();
        }
        return true;
    }

    function logAction($action)
    {
        $userId = Yii::$app->user->id;
        $zone = new \DateTimeZone('Asia/Almaty');
        $currentDate = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');

        $log = new Changelog();
        $log->entity_id = $this->owner->id;
        $log->user_id = $userId;
        $log->tablename = $this->owner->tableName();
        $log->datetime = $currentDate;
        $log->type = $action;
        if ($log->save()) {
            return true;
        }
    }


}