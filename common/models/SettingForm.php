<?php

namespace common\models;

use yii\base\Model;
use Yii;

class SettingForm extends Model {

    public $adminEmail;
    public $answerLength;

    public function rules()
    {
        return [
            [['adminEmail'], 'email'],
            [['answerLength'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'adminEmail' => Yii::t('app', 'admin email'),
            'answerLength' => Yii::t('app', 'answer length'),
        ];
    }

}
