<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use Yii;

/**
 * This is the model class for table "survey_filial".
 *
 * @property int $id
 * @property int $survey_id
 * @property int $filial_id
 * @property int $task_count
 * @property string $comment
 * @property int $supervizer_Id
 * @property string $instruction
 *
 * @property Survey $survey
 * @property Filial $filial
 * @property User $supervizer
 */
class SurveyFilial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'survey_filial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['survey_id', 'filial_id', 'task_count', 'supervisor_id', 'id_scenario'], 'required'],
            [['survey_id', 'filial_id', 'supervisor_id', 'id_scenario'], 'integer'],
            ['task_count', 'integer', 'min' => 1],
            ['comment', 'string', 'max' => 1024],
            ['instruction', 'string', 'max' => 260],
            [['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_id' => 'id']],
            [['filial_id'], 'exist', 'skipOnError' => true, 'targetClass' => Filial::className(), 'targetAttribute' => ['filial_id' => 'id']],
            [['supervisor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['supervisor_id' => 'id']],
            [['id_scenario'], 'exist', 'skipOnError' => true, 'targetClass' => Scenario::className(), 'targetAttribute' => ['id_scenario' => 'id']],
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
            'survey_id' => Yii::t('app', 'Survey ID'),
            'filial_id' => Yii::t('app', 'Filial ID'),
            'task_count' => Yii::t('app', 'Task Count'),
            'comment' => Yii::t('app', 'Comment'),
            'supervisor_id' => Yii::t('app', 'Supervizor ID'),
            'scenario_id' => Yii::t('app', 'Сценарий'),
            'instruction' => Yii::t('app', 'Инструкция')
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
    public function getFilial()
    {
        return $this->hasOne(Filial::className(), ['id' => 'filial_id']);
    }

    public function getFilialCityLabel()
    {
        return $this->filial->getCityLabel();
    }

    public function getFilialAddress()
    {
        return $this->filial->address;
    }

    public function getFilialPhone()
    {
        return $this->filial->phone;
    }

    public function getFilialChief()
    {
        return $this->filial->chief_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupervisor()
    {
        return $this->hasOne(User::className(), ['id' => 'supervisor_id']);
    }


    public function getSurveyScenario()
    {
        return $this->hasOne(Scenario::className(), ['id' => 'id_scenario']);
    }


    public function hasFreeMsSurveys()
    {
        $msSurveys = MsSurvey::find()->where(['survey_filial' => $this->id, 'status' => MsSurvey::STATUS_NEW])->count();
        return $msSurveys ?: false;
    }
}
