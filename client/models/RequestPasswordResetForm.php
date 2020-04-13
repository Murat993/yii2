<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 05.07.18
 * Time: 10:23
 */

namespace client\models;


use common\models\User;
use yii\base\Model;

class RequestPasswordResetForm extends Model
{
    public $email;
    public $user;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'isUserExists'],
        ];
    }

    public function isUserExists($attribute, $params){
        if (!$this->hasErrors()) {
            $user = User::find()
                ->where(['email' => $this->email])
                ->andWhere(['or',
                    ['role' => User::ROLE_CLIENT_USER],
                    ['role' => User::ROLE_CLIENT_SUPER],
                ])->one();
            if (!$user) {
                $this->addError($attribute, 'Пользователя с данным email не существует.');
            }else{
                $this->user = $user;
            }
        }
    }

}