<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Changelog as ChangelogModel;
use yii\helpers\BaseArrayHelper;

/**
 * Changelog represents the model behind the search form of `common\models\Changelog`.
 */
class ChangelogSearch extends ChangelogModel
{
    public $date_from;
    public $date_to;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'entity_id', 'type'], 'integer'],
            [['tablename', 'field_name', 'old_value', 'new_value', 'datetime'], 'safe'],
            [['date_from', 'date_to'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ChangelogModel::find()->joinWith(['user']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => false
        ]);

        $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'entity_id' => $this->entity_id,
            'type' => $this->type,
            //  'datetime' => $datetime,
        ]);

        if ($params['ChangelogSearch']['date_from']) {
            $date_from = \DateTime::createFromFormat('Y/m/d', $params['ChangelogSearch']['date_from'])->format('Y-m-d');
            $query->andWhere(['>=', 'datetime', $date_from]);
        }

        if ($params['ChangelogSearch']['date_to']){
            $date_to = \DateTime::createFromFormat('Y/m/d', $params['ChangelogSearch']['date_to'])->format('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_to. ' + 1 day'));
            $query->andWhere(['<=', 'datetime', $date_to]);
        }

        if (! $date_from && ! $date_to) {
            $today = date("Y-m-d");
            $monthBefore = (new \DateTime($today))->modify('-1 week')->format('Y-m-d');
            $date_from = $monthBefore;
            $date_to = $today;
            $query->andWhere(['between', 'datetime', $date_from, $date_to]);
        }

        $query->andFilterWhere(['like', 'tablename', $this->tablename])
            ->andFilterWhere(['like', 'field_name', $this->field_name])
            ->andFilterWhere(['like', 'old_value', $this->old_value])
            ->andFilterWhere(['like', 'new_value', $this->new_value]);

        $query->orderBy('datetime DESC');

        return $dataProvider;
    }
}
