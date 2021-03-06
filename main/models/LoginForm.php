<?php
namespace main\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['email', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['email', 'validateClient'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Неверное имя пользователя или пароль.'));
            }
        }
    }

    /**
     * Проверка статуса компании
     *
     * @param $attribute
     * @param $params
     */
    public function validateClient($attribute, $params)
    {
        if(! Yii::$app->userService->checkClient($this)){
            $this->addError($attribute, Yii::t('app', 'Ваша организация деактивирована.'));
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $zone = new \DateTimeZone('Asia/Almaty');
            $currentDate = (new \DateTime(date('Y-m-d H:i:s')))->setTimezone($zone)->format('Y-m-d H:i:s');
            if (Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0)){
                $this->_user->last_login = $currentDate;
                return $this->_user->save();
            }
        }
        
        return false;
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
}
