<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use Yii;

/**
 * This is the model class for table "tariff".
 *
 * @property int $id
 * @property int $name
 *
 * @property Client[] $clients
 */
class Tariff extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 25],
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
            'name' => Yii::t('app', 'Название'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Client::className(), ['tariff_id' => 'id']);
    }
}
