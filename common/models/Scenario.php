<?php

namespace common\models;

use common\behaviors\ChangelogBehavior;
use Yii;

/**
 * This is the model class for table "scenario".
 *
 * @property int $id
 * @property string $name
 */
class Scenario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scenario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'id_client'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['id_client'], 'integer']
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
            'id' => 'ID',
            'name' => Yii::t('app', 'Название'),
        ];
    }
}
