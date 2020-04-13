<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use Yii;

/**
 * This is the model class for table "task_answer".
 *
 * @property int $id
 * @property string $file
 * @property int $reject
 * @property string $comment
 * @property int $ms_id
 * @property int $ms_survey_id
 * @property int $task_id
 *
 * @property User $ms
 * @property Task $task
 */
class TaskAnswer extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reject', 'ms_id', 'task_id', 'ms_survey_id'], 'integer'],
            [['ms_id', 'task_id', 'ms_survey_id'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false,
                'maxFiles' => 20,
                'extensions' => 'png, jpg, jpeg', 'on' => Task::PHOTO,
                'when' => function (){
                    return $this->reject == 0;
                },
                'whenClient' => "function(attribute, value){
                    return !$('#reject-cbx').is(':checked');
                }"
            ],
            [['file'], 'file', 'skipOnEmpty' => false,
                'maxFiles' => 20,
                'extensions' => 'mp3, wav, amr, m4a, ogg, aac', 'on' => Task::AUDIO,
                'when' => function (){
                    return $this->reject == 0;
                },
                'whenClient' => "function(attribute, value){
                    return !$('#reject-cbx').is(':checked');
                }"
            ],
            ['comment', 'required', 'when' => function(){
                    return $this->reject == 1;
                },
                'whenClient' => "function(attribute, value){
                    return $('#reject-cbx').is(':checked');
                }"
            ],
            [['comment'], 'string', 'max' => 1024],
            [['ms_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ms_id' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['ms_survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => MsSurvey::className(), 'targetAttribute' => ['ms_survey_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ChangelogBehavior::className(),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file' => Yii::t('app', 'Файл'),
            'reject' => Yii::t('app', 'Не могу выполнить задание'),
            'comment' => Yii::t('app', 'Комментарий'),
            'ms_id' => Yii::t('app', 'Ms ID'),
            'task_id' => Yii::t('app', 'Task ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMs()
    {
        return $this->hasOne(User::className(), ['id' => 'ms_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    public function getMsSurvey()
    {
        return $this->hasOne(MsSurvey::className(), ['id' => 'ms_survey_id']);
    }

}
