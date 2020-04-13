<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task_file".
 *
 * @property int $id                    - Идентификатор
 * @property int $task_id               - Идентификатор задания
 * @property int $type                  - Тип задания
 * @property int $ms_survey_id          - Связь пользователя с заданием
 * @property string $file_name          - Название файла
 *
 * @property Task $task
 */
class TaskFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'type', 'file_name'], 'required'],
            [['task_id', 'type', 'ms_survey_id'], 'integer'],
            [['file_name'], 'string', 'max' => 256],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'task_id' => Yii::t('app', 'Задание'),
            'type' => Yii::t('app', 'Тип'),
            'file_name' => Yii::t('app', 'Файл'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }
}
