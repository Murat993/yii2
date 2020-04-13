<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use common\translate\TranslatedBehavior;
use common\translate\TranslatedTrait;
use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $name
 * @property int $filetype
 * @property string $comment
 * @property int $survey_id
 *
 * @property Survey $survey
 * @property TaskAnswer[] $taskAnswers
 */
class Task extends \yii\db\ActiveRecord
{
    const AUDIO = 1;
    const PHOTO = 2;

    use TranslatedTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'filetype'], 'required'],
            [['filetype', 'survey_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 1024],
            [['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TranslatedBehavior::className(),
                'translateRelation' => 'taskLang', // Specify the name of the connection that will store transfers
                //  'languageAttribute' => 'lang_id', // post_lang field from the table that will store the target language
                'translateAttributes' => [
                    'name',
                    'comment'
                ]
            ],
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
            'name' => Yii::t('app', 'Название'),
            'filetype' => Yii::t('app', 'Тип отчета'),
            'comment' => Yii::t('app', 'Комментарий'),
            'survey_id' => Yii::t('app', 'Анкетирование'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Survey::className(), ['id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAnswers()
    {
        return $this->hasMany(TaskAnswer::className(), ['task_id' => 'id']);
    }

    public function getTaskAnswer($ms_id, $ms_survey_id)
    {
        return TaskAnswer::find()->where(['task_id' => $this->id, 'ms_id' => $ms_id, 'ms_survey_id' => $ms_survey_id])->one();
    }

    public static function getTasksByType($type, $mc_survey)
    {
        return TaskFile::findAll(['ms_survey_id' => $mc_survey, 'type' => $type]);
    }

    public function isTaskAnswerFileByMsId($ms_id, $ms_survey_id)
    {
        $model = $this->getTaskAnswer($ms_id, $ms_survey_id);
        if ($model) {
            return $model->file;
        }
        return false;
    }

    public function isTaskAnswerRejectedByMsId($ms_id, $ms_survey_id)
    {
        $model = $this->getTaskAnswer($ms_id, $ms_survey_id);
        if ($model) {
            return $model->reject;
        }
        return false;
    }

    public function getTaskAnswerCommentByMsId($ms_id, $ms_survey_id)
    {
        $model = $this->getTaskAnswer($ms_id, $ms_survey_id);
        if ($model) {
            return $model->comment;
        }
    }

    public function getTaskLang()
    {
        return $this->hasMany(TaskLang::className(), ['task_id' => 'id']);
    }

    /**
     * Проверяем ответ и файлы
     * @param $ms_id
     * @param $ms_survey_id
     * @return bool|mixed
     */
    public function checkAnswer($ms_id, $ms_survey_id)
    {
        $model = $this->getTaskAnswer($ms_id, $ms_survey_id);
        $files = TaskFile::find()->where(['task_id'=> $this->id, 'ms_survey_id' => $ms_survey_id])->exists();
        if (!$model->reject && $files) {
            return true;
        }
        return false;
    }
}
