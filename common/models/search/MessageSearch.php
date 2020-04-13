<?php

namespace common\models\search;

use common\models\Message;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FilialSearch represents the model behind the search form of `common\models\Filial`.
 */
class MessageSearch extends Message
{
    public $date_from;
    public $date_to;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id', 'read'], 'integer'],
            [['text','time','from'], 'safe'],
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
        $query = Message::find()->groupBy('chat_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'chat_id' => $this->chat_id,
            'read' => $this->read,
        ]);
        if ( $params['MessageSearch']['date_from'] &&  $params['MessageSearch']['date_to']){

            $date_from = \DateTime::createFromFormat('Y/m/d', $params['MessageSearch']['date_from'])->format('Y-m-d');
            $date_to = \DateTime::createFromFormat('Y/m/d', $params['MessageSearch']['date_to'])->format('Y-m-d');
            $query->andWhere(['between', 'time', $date_from, $date_to ]);
        }

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'from', $this->from]);

        return $dataProvider;
    }
}
