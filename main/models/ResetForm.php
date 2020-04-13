<?php
namespace main\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ResetForm extends Model
{
    public $email;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email'], 'required'],
            // rememberMe must be a boolean value
            // password is validated by validatePassword()
            [['email'], 'email'],
            [['email'], 'validateUser'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->userService->findByEmail($this->email);
        }

        return $this->_user;
    }

    public function validateUser($attribute, $params)
    {
        if ($this->getUser()){
            return true;
        }else{
            $this->addError($attribute, Yii::t('app', 'User not exists'));
            return false;
        }
    }

}
