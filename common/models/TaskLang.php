<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use common\translate\models\Lang;
use Yii;

/**
 * This is the model class for table "task_lang".
 *
 * @property int $id
 * @property int $task_id
 * @property string $lang_id
 * @property string $name
 * @property string $comment
 *
 * @property Task $task
 * @property myLang $lang
 */
class TaskLang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_lang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'lang_id', 'name'], 'required'],
            [['task_id'], 'integer'],
            [['lang_id'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 1024],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lang::className(), 'targetAttribute' => ['lang_id' => 'id']],
        ];
    }

//TODO log langs
//    public function behaviors()
//    {
//        return [
//            [
//                'class' => ChangelogBehavior::className(),
//            ]
//        ];
//    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task_id' => Yii::t('app', 'Task ID'),
            'lang_id' => Yii::t('app', 'Lang ID'),
            'name' => Yii::t('app', 'Name'),
            'comment' => Yii::t('app', 'Comment'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Lang::className(), ['id' => 'lang_id']);
    }
}
