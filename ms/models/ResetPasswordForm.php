<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 05.07.18
 * Time: 12:06
 */

namespace ms\models;


use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $confirm;

    public function rules()
    {
        return [
            [['password', 'confirm'], 'required'],
            ['confirm', 'matchPasswords']
        ];
    }

    public function matchPasswords($attribute, $params){
        if (!$this->hasErrors()) {
            if($this->password != $this->confirm){
                $this->addError($attribute, \Yii::t('app', 'Пароли не совпадают.'));
            }
        }
    }


    public function attributeLabels(){
        return [
            'password' => \Yii::t('app', 'Пароль'),
            'confirm' => \Yii::t('app', 'Подтвердить пароль'),
        ];
    }

}