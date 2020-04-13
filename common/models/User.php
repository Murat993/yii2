<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $password
 * @property int $status
 * @property string $comment
 * @property string $registration_date
 * @property string $last_login
 * @property int $role_id
 * @property boolean $agreement_accepted
 *
 * @property Changelog[] $changelogs
 * @property ClientUser[] $clientUsers
 * @property GeoUser[] $geoUsers
 * @property MsSurvey[] $msSurveys
 * @property QuestionAnswer[] $questionAnswers
 * @property QusetionCheck[] $qusetionChecks
 * @property SurveyFilial[] $surveyFilials
 * @property TaskAnswer[] $taskAnswers
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $city_id;
    public $country_id;
    const STATUS_ACTIVE = 1;
    const STATUS_DEACTIVATED = 2;
    const STATUS_DELETED = 9;

    const ROLE_ADMIN = 111;
    const ROLE_SUPERVISOR = 333;
    const ROLE_SUPERVISOR_GLOBAL = 666;
    const ROLE_MYSTIC = 222;
    const ROLE_MYSTIC_GLOBAL = 777;
    const ROLE_CLIENT_USER = 444;
    const ROLE_CLIENT_SUPER = 555;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email'], 'required', 'message' => 'Поле не может быть пустым!'],
            [['status', 'role', 'city_id', 'country_id'], 'integer'],
            [['agreement_accepted'], 'boolean'],
            [['registration_date', 'last_login'], 'safe'],
            [['name', 'email', 'password', 'auth_key', 'reset_token'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 15],
            [['comment'], 'string', 'max' => 255],
            ['phone', 'validatePhone'],
            ['email', 'unique'],
            ['email', 'email']
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
            'name' => Yii::t('app', 'ФИО'),
            'phone' => Yii::t('app', 'Телефон'),
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Пароль'),
            'city_id' => Yii::t('app', 'Город'),
            'country_id' => Yii::t('app', 'Страна'),
            'status' => Yii::t('app', 'Статус'),
            'comment' => Yii::t('app', 'Комментарий'),
            'registration_date' => Yii::t('app', 'Дата регистрации'),
            'last_login' => Yii::t('app', 'Последний вход'),
            'role' => Yii::t('app', 'Роль'),
            'auth_key' => Yii::t('app', 'Ключ авторизации'),
        ];
    }

    public function beforeDelete()
    {
        Yii::$app->authManager->revokeAll($this->id);

        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChangelogs()
    {
        return $this->hasMany(Changelog::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientUser()
    {
        return $this->hasOne(ClientUser::className(), ['user_id' => 'id']);
    }

    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id'])
            ->via('clientUser');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeoUsers()
    {
        return $this->hasMany(GeoUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMsSurveys()
    {
        return $this->hasMany(MsSurvey::className(), ['ms_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionAnswers()
    {
        return $this->hasMany(QuestionAnswer::className(), ['ms_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionChecks()
    {
        return $this->hasMany(QuestionCheck::className(), ['supervisor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurveyFilials()
    {
        return $this->hasMany(SurveyFilial::className(), ['supervisor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAnswers()
    {
        return $this->hasMany(TaskAnswer::className(), ['ms_id' => 'id']);
    }

    public function getChatAdmin()
    {
        return $this->hasOne(Chat::className(), ['user_admin' => 'id']);
    }

    public function getChatClient()
    {
        return $this->hasOne(Chat::className(), ['user_client' => 'id']);
    }

    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['from' => 'id']);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    public static function findUsernameById($id)
    {
        $user = self::findOne(['id' => $id]);
        if ($user->role === User::ROLE_ADMIN) {
            return 'PLAN MSP';
        }
        return $user->name;
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }


    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePhone()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        return $this->phone = $phone;

    }

    public function generateRecoveryToken()
    {
        $hash1 = md5(rand(0, 9999999) . date("H:i:s"));
        $hash2 = md5(rand(0, 9999999) . date("H:i:s"));
        return $hash1 . $hash2;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }


    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function generatePass()
    {
        return Yii::$app->security->generateRandomString(8);
    }

    public function setRole($role)
    {
        return Yii::$app->authService->givePermissions($this->id, $role);
    }

    public function getClientId()
    {
        $clientUser = ClientUser::findOne(['user_id' => $this->id]);
        if ($clientUser) {
            return $clientUser->client_id;
        }
    }

    public function linkCity()
    {
        if ($this->city_id) {
            $geoUser = new GeoUser();
            $geoUser->user_id = $this->id;
            $geoUser->geo_unit_id = $this->city_id;
            return $geoUser->save();
        }
    }

}
