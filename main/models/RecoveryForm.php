<?php
namespace main\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class RecoveryForm extends Model
{
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password'], 'required'],
            [['password'], 'string', 'max' => 100]
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Password'),
        ];
    }


}
