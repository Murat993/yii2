<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use common\translate\models\Lang;
use Yii;

/**
 * This is the model class for table "survey_lang".
 *
 * @property int $id
 * @property int $survey_id
 * @property string $lang_id
 * @property string $name
 * @property string $comment
 * @property string $instruction
 * @property string $report_req
 *
 * @property Survey $survey
 * @property myLang $lang
 */
class SurveyLang extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'survey_lang';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['survey_id', 'lang_id', 'name'], 'required'],
            [['survey_id'], 'integer'],
            [['lang_id'], 'string', 'max' => 2],
            [['name', 'instruction'], 'string', 'max' => 255],
            [['comment', 'report_req'], 'string', 'max' => 1024],
            [['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::className(), 'targetAttribute' => ['survey_id' => 'id']],
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
            'survey_id' => Yii::t('app', 'Survey ID'),
            'lang_id' => Yii::t('app', 'Lang ID'),
            'name' => Yii::t('app', 'Name'),
            'comment' => Yii::t('app', 'Comment'),
            'instruction' => Yii::t('app', 'Instruction'),
            'report_req' => Yii::t('app', 'Report Req'),
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
    public function getLang()
    {
        return $this->hasOne(Lang::className(), ['id' => 'lang_id']);
    }
}
