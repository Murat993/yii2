<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use common\translate\TranslatedBehavior;
use common\translate\TranslatedTrait;
use Yii;

/**
 * This is the model class for table "survey".
 *
 * @property int $id
 * @property string $name
 * @property string $survey_from
 * @property string $survey_to
 * @property string $comment
 * @property string $instruction
 * @property int $questionary_id
 * @property int $client_id
 *
 * @property MsSurvey[] $msSurveys
 * @property QuestionAnswer[] $questionAnswers
 * @property Questionary $questionary
 * @property Client $client
 * @property SurveyFilial[] $surveyFilials
 * @property Task[] $tasks
 */
class Survey extends \yii\db\ActiveRecord
{
    //virtual
    public $task_count;


    use TranslatedTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'survey';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'survey_from', 'survey_to', 'questionary_id', 'client_id', 'report_date'], 'required'],
            [['survey_from', 'survey_to', 'report_date'], 'safe'],
            [['questionary_id', 'client_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [[ 'instruction'], 'file', 'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, txt, doc, docx, pdf, rtf'],
            [['report_to_email'], 'boolean'],
            [['comment', 'report_req'], 'string', 'max' => 1024],
            [['questionary_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questionary::className(), 'targetAttribute' => ['questionary_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TranslatedBehavior::className(),
                'translateRelation' => 'surveyLang', // Specify the name of the connection that will store transfers
                'languageAttribute' => 'lang_id', // post_lang field from the table that will store the target language
                'translateAttributes' => [
                    'name',
                    'comment',
                    'instruction',
                    'report_req'
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
            'survey_from' => Yii::t('app', 'Период с'),
            'survey_to' => Yii::t('app', 'до'),
            'comment' => Yii::t('app', 'Комментарий'),
            'instruction' => Yii::t('app', 'Инструкция'),
            'questionary_id' => Yii::t('app', 'Шаблон'),
            'client_id' => Yii::t('app', 'Клиент'),
            'report_date' => Yii::t('app', 'Предоставить отчет до'),
            'report_to_email' => Yii::t('app', 'Отправить на email'),
            'report_req' => Yii::t('app', 'Требования к отчету'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMsSurveys()
    {
        return $this->hasMany(MsSurvey::className(), ['survey_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionAnswers()
    {
        return $this->hasMany(QuestionAnswer::className(), ['survey_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionary()
    {
        return $this->hasOne(Questionary::className(), ['id' => 'questionary_id']);
    }

    public function getQuestionaryLabel()
    {
        return $this->questionary->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }

    public function getClientLabel()
    {
        return $this->client->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyFilials()
    {
        return $this->hasMany(SurveyFilial::className(), ['survey_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['survey_id' => 'id']);
    }


    public function getSurveyLang()
    {
        return $this->hasMany(SurveyLang::className(), ['survey_id' => 'id']);
    }

    public function getMsSurveyCount()
    {
     return $this->getSurveyFilials()->count();
    }

}
