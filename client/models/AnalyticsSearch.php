<?php

namespace client\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * SearchModel - для аналитики у клиента
 */
class AnalyticsSearch extends Model
{
    public $date_from;
    public $survey_ids;
    public $filial_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                'date_from',
                'string',
                'length' => 23
            ],
            [
                [
                    'survey_ids',
                    'filial_ids',
                ],
                'each',
                'rule' => [
                    'integer'
                ]
            ],
        ];
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
        $this->load($params);
        if (!$this->validate()) {
            throw new NotFoundHttpException(Yii::t('app', 'Неверные параметры'));
        }

        $clientId = Yii::$app->user->identity->getClientId();

        if (!empty($params['AnalyticsSearch']['survey_ids']) && is_array($params['AnalyticsSearch']['survey_ids']))
        {
            $this->survey_ids = implode(",", $params['AnalyticsSearch']['survey_ids']);
            if (! empty($this->survey_ids)) {
                $searchSurvey = "AND s.id in ({$this->survey_ids})";
            }
        }

        if (!empty($params['AnalyticsSearch']['filial_ids']) && is_array($params['AnalyticsSearch']['filial_ids']))
        {
            $this->filial_ids = implode(",", $params['AnalyticsSearch']['filial_ids']);
            if (! empty($this->filial_ids)) {
                $searchFilial = "AND sf.filial_id in ({$this->filial_ids})";
            }
        }

        if ($params['AnalyticsSearch']['date_from']){
            $date_from = date('Y-m-d', strtotime(substr($params['AnalyticsSearch']['date_from'], 0,10)));
            $date_to = date('Y-m-d', strtotime(substr($params['AnalyticsSearch']['date_from'],13)));
            if($date_to != $date_from){
                $this->date_from = " and (`s`.`survey_from` >= '{$date_from}') AND (`s`.`survey_to` <= '{$date_to}') ";
            } else {
                $this->date_from = null;
            }
        }

        if (! $searchSurvey && $searchFilial) {
            $select = "q1.percent_comlpete as percent_complete,
        q1.filial_count as filial_count,
        q1.emp_count as employee_count,
        q1.anket_count as ms_surveys_count,
        q1.anket_count*q1.question_count as question_count,
        q1.real_points as real_points,
        (q1.anket_count*q1.max_points) as max_points,
        q1.survey_id as survey_id";
        } else {
            $select = "AVG(q1.percent_comlpete) as percent_complete,
        SUM(q1.filial_count) as filial_count,
        SUM(q1.emp_count) as employee_count,
        SUM(q1.anket_count) as ms_surveys_count,
        SUM(q1.anket_count*q1.question_count) as question_count,
        sum(q1.real_points) as real_points,
        (q1.anket_count*q1.max_points) as max_points";
        }

        $queryText = "{$select}
        from
        (
        select
        getRealPercentBySurvey(s.id) as percent_comlpete,
        getMaxPointsBySurvey(s.id) as max_points,
        getFilialCountBySurvey(s.id) as filial_count,
        getEmployeeCountBySurvey(s.id) as emp_count,
        getRealPointsBySurvey(s.id) as real_points,
        s.id as survey_id,
        ( 
        select
        count(*)
        from
        ms_survey ms,
        survey_filial sf
        where
        ms.survey_filial = sf.id
        and sf.survey_id = s.id
        and ms.status = 555) as anket_count,
        (
        select
        count(*)
        from
        question q,
        article a
        where
        q.article_id = a.id
        and a.questionary = s.questionary_id) question_count
        from
        survey s
        where
        exists (
        select
        *
        from
        ms_survey ms,
        survey_filial sf
        where
        ms.survey_filial = sf.id
        and sf.survey_id = s.id
        and ms.status = 555)
        and s.client_id = {$clientId} {$this->date_from} {$searchSurvey}) q1" ;

        $query = (new Query())->select([
            $queryText
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);

        return $dataProvider;
    }
}
