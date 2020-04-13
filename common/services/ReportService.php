<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 03.10.2018
 * Time: 10:53
 */

namespace common\services;


use yii\base\Component;
use yii\db\Query;
use common\models\MsSurvey;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ReportService extends Component
{
    const TYPE_BY_CITY = 1;
    const TYPE_BY_FILIAL = 2;
    private $queryBuilder;
    private $finished = MsSurvey::STATUS_COMPLETED;

    public function __construct()
    {
        $this->queryBuilder = new Query();
    }

    /**
     * @param $client_id
     * @return \yii\db\Command
     */
    public function lastComments($client_id)
    {
        $query = \Yii::$app->db->createCommand("select * from 
            (select ms.id, (select max(msg.`time`) from message msg where  msg.chat_id = ch.id) as last_msg_datetime,
            (select count( *) from message msg where  msg.chat_id = ch.id) as msg_count,
            (select count( *) from task_answer ta where ta.ms_survey_id = ms.id) as task_count,
            (select gu.name from geo_unit gu where gu.id = f.city_id) as city_name, f.name,
            (select name from employee where id = ms.employee_id) as employe_name,
            (getRealPointsBySurveyMS(s.id, ms.id)*100)/getMaxPointsBySurvey(s.id) as percent,
            (select msg.`text` from message msg where  msg.chat_id = ch.id order by msg.`time` DESC LIMIT 1) as msg_text
            
            from survey s, ms_survey ms, survey_filial sf, filial f, chat ch
            where 
            sf.survey_id = s.id
            and ms.survey_filial = sf.id
            and f.id = sf.filial_id
            and ms.status = 555
            and s.client_id = {$client_id}
            and (select count( *) from message msg where  msg.chat_id = ch.id) > 0
            and ch.ms_survey_id = ms.id) q1
            order by q1.last_msg_datetime DESC");
        return $query;
    }


    public function objectsMain($client_id, $searchSurvey = null, $searchFilial = null, $date = null)
    {
        $query = $this->queryBuilder->select([
            "q1.filial_id, f.name, (select gu.name from geo_unit gu where gu.id = f.city_id) as city, 
            f.address, AVG(q1.percent) as percent, count(*) as total
            from (select sf.filial_id, 
            (getRealPointsBySurveyMS(sf.survey_id, ms.id)*100)/getMaxPointsBySurvey(sf.survey_id) as percent
            from survey s, ms_survey ms, survey_filial sf
            where s.id = sf.survey_id
            {$searchSurvey} {$searchFilial} {$date}
            and ms.survey_filial = sf.id
            and ms.status = {$this->finished}
            and s.client_id = {$client_id}) q1, filial f
            where q1.filial_id = f.id group by q1.filial_id order by AVG(q1.percent) DESC"
        ]);
        return $query;
    }

    public function objectsMainBySurvey($client_id, $survey_id = null)
    {

        if ($survey_id) {
            $andWhere = "and s.id = {$survey_id}";
        } else {
            $andWhere = "and s.client_id = {$client_id}";
        }

        $query = $this->queryBuilder->select([
            "q1.filial_id, f.name, (select gu.name from geo_unit gu where gu.id = f.city_id) as city, 
            f.address, AVG(q1.percent) as percent, count(*) as total
            from (select sf.filial_id, 
            (getRealPointsBySurveyMS(sf.survey_id, ms.id)*100)/getMaxPointsBySurvey(sf.survey_id) as percent
            from survey s, ms_survey ms, survey_filial sf
            where s.id = sf.survey_id
            and ms.survey_filial = sf.id
            and ms.status = {$this->finished}
            {$andWhere})q1, filial f
            where q1.filial_id = f.id group by q1.filial_id order by AVG(q1.percent) DESC"
        ]);
        return $query;
    }

    public function objectsByCities($client_id, $searchSurvey = null, $searchFilial = null, $date = null)
    {
        $query = $this->queryBuilder->select([
            "q1.city_id, gu.name as city_name,   AVG(q1.percent) as percent, count( *) as count
            from (select f.city_id, 
            (getRealPointsBySurveyMS(sf.survey_id, ms.id)*100)/getMaxPointsBySurvey(sf.survey_id) as percent 
            from survey s, ms_survey ms, survey_filial sf, filial f
            where s.id = sf.survey_id
            {$searchSurvey} {$searchFilial} {$date}
            and ms.survey_filial = sf.id
            and f.id = sf.filial_id
            and ms.status = {$this->finished}
            and s.client_id = {$client_id}) q1, geo_unit gu 
            where q1.city_id = gu.id
            group by q1.city_id
            order by AVG(q1.percent) DESC"
        ]);
        return $query;
    }

    public function summary($client_id)
    {
        $query = $this->queryBuilder->select([
            "a.id, a.parent_id, al.name, getRealFullPointsBySurveyArticle(s.id, a.id) as real_point,
            getFullPointsBySurveyArticle(s.id, a.id)*getCountBySurveyArticle(s.id, a.id) as max_point,
            ifnull((getRealFullPointsBySurveyArticle(s.id, a.id)*100)/
            (getFullPointsBySurveyArticle(s.id, a.id)*getCountBySurveyArticle(s.id, a.id)),0) as percent,
            'article' as tableName
            from article a, article_lang al, survey s
            where a.id = al.article_id and  al.lang_id = 'ru'  
            and s.questionary_id = a.questionary
            and a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
            where sf.id = ms.survey_filial 
            and s.id = sf.survey_id
            and s.client_id = {$client_id})
            union ALL
            select  q.id, a.id, ql.name, getRealFullPointsByMSSurveyQuestion(s.id, q.id),
            getFullPointsBySurveyQuestion(s.id, q.id)*getCountBySurveyQuestion(s.id, q.id),
            ifnull((getRealFullPointsByMSSurveyQuestion(s.id, q.id)*100)/
            (getFullPointsBySurveyQuestion(s.id, q.id)*getCountBySurveyQuestion(s.id, q.id)),0) as percent,
            'question' as tableName
            from article a, question q, question_lang ql, survey s
            where q.id = ql.question_id and  ql.lang_id = 'ru' 
            and s.questionary_id = a.questionary
            and q.article_id = a.id and a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
            where sf.id = ms.survey_filial 
            and s.id = sf.survey_id and s.client_id = {$client_id})"
        ]);
        return $query;
    }

    public function surveys($client_id, $survey_id = null, int $limit = null)
    {
        if ($survey_id) {
            $andWhere = "and s.id = {$survey_id}";
        } else {
            $andWhere = "and s.client_id = {$client_id}";
        }

//        if ($limit){
//            $andWhere .= ' LIMIT 50';
//        }

        $query = $this->queryBuilder->select([
            "* from 
            (select ms.id, ms.comment, (select count(*) from message msg where (select id from chat where chat.ms_survey_id = ms.id and msg.chat_id = chat.id)) as comment_count,
            (select count( *) from task_answer ta where ta.ms_survey_id = ms.id) as task_count,
            (select max(msd.`date`) from ms_survey_date msd where msd.ms_survey_id = ms.id and msd.type = 111) as complete_date,
            (select gu.name from geo_unit gu where gu.id = f.city_id) as city_name, f.name as filial_name,
            (select name from employee where id = ms.employee_id) as employee_name,
            (getRealPointsBySurveyMS(s.id, ms.id)*100)/getMaxPointsBySurvey(s.id) as percent,
            (select sl.name from survey_lang sl where sl.survey_id = s.id and sl.lang_id = 'ru') as survey_name,
            getRealPointsBySurveyMS(s.id, ms.id) as real_point, getMaxPointsBySurvey(s.id) as max_points,
             (select name from scenario where scenario.id = sf.id_scenario ) as scenario
            from survey s, ms_survey ms, survey_filial sf, filial f
            where 
            sf.survey_id = s.id
            and ms.survey_filial = sf.id
            and f.id = sf.filial_id
            and ms.status = {$this->finished}
            {$andWhere}) q1
            order by q1.complete_date DESC"
        ]);
//        var_dump($query->createCommand()->getRawSql());die;
        return $query;
    }


    public function survey($id)
    {
        if (!is_numeric($id)) {
            throw new NotFoundHttpException(\Yii::t('app', 'Неверные параметры'));
        }
        $query = $this->queryBuilder->select([
            "* from 
            (select ms.id, ms.comment, (select count(*) from  message msg where  msg.chat_id = ms.id) as comment_count,
            (select count( *) from task_answer ta where ta.ms_survey_id = ms.id) as task_count,
            (select max(msd.`date`) from ms_survey_date msd where msd.ms_survey_id = ms.id and msd.type = 111) as complete_date,
            (select gu.name from geo_unit gu where gu.id = f.city_id) as city_name, f.name as filial_name,
            (select name from employee where id = ms.employee_id) as employee_name,
            (getRealPointsBySurveyMS(s.id, ms.id)*100)/getMaxPointsBySurvey(s.id) as percent,
            (select sl.name from survey_lang sl where sl.survey_id = s.id and sl.lang_id = 'ru') as survey_name,
            getRealPointsBySurveyMS(s.id, ms.id) as real_point, getMaxPointsBySurvey(s.id) as max_points,
             (select name from scenario where scenario.id = sf.id_scenario ) as scenario
            from survey s, ms_survey ms, survey_filial sf, filial f
            where 
            sf.survey_id = s.id
            and ms.survey_filial = sf.id
            and f.id = sf.filial_id
            and ms.status = {$this->finished}) q1
            where q1.id = {$id}"
        ]);
        return $query;
    }

    public function employees($client_id)
    {
        $query = $this->queryBuilder->select([
            "q1.employe_name as employee, avg(q1.percent) as percent, count( *) as count
            from (select (select name from employee where id = mss.employee_id) as employe_name, sf.survey_id, mss.id,
            (getRealPointsBySurveyMS(sf.survey_id, mss.id)*100)/getMaxPointsBySurvey(sf.survey_id) as percent
            from ms_survey mss, survey_filial sf, filial, geo_unit gu, survey s
            where sf.id = mss.survey_filial
            and filial.id = sf.filial_id
            and gu.id = filial.city_id
            and s.id = sf.survey_id
            and mss.employee_id is not null
            and s.client_id =  {$client_id}
            and mss.status = {$this->finished} ) q1
            group by q1.employe_name
            order by AVG(q1.percent) DESC"
        ]);
        return $query;
    }

    public function pivot($client_id)
    {
        $query = $this->queryBuilder->select([
            " (select concat(QUARTER(max(msd.`date`)), ' квартал ',YEAR(max(msd.`date`))) from ms_survey_date msd where msd.ms_survey_id = ms.id ) as 'Квартал',
            (select max(`date`) from ms_survey_date msd where msd.ms_survey_id = ms.id and msd.type = 111) as 'Дата визита',
            (select sl.name from survey_lang sl where sl.survey_id = s.id and sl.lang_id = 'ru') as 'Анкетирование',
            (select name from employee where id = ms.employee_id) as 'Работник',
            f.name as 'Объект',
            sc.name as 'Сценарий',
            (select gu.name from geo_unit gu where gu.id = f.city_id) as 'Город',
            (select fsu.name from filial_structure_unit fsu where fsu.id = f.filial_structure_unit_id) as 'Структура',
            (select an.name from article_lang an where an.lang_id ='ru' and an.article_id = q.article_id)  as 'Раздел',
            ql.name as 'Вопрос', 
            (select aol.`text` from answer_option_lang aol where aol.answer_option_id = qc.answer_option_id ) as 'Ответ',
            qa.text as 'Комментарий ТП',
            getRealPointsByMSSurveyQuestion(ms.id, q.id) as 'Кол-во Баллов',
            getFullPointsBySurveyQuestion(s.id, q.id) as 'Максимальный Балл',
            ifnull((getRealPointsByMSSurveyQuestion(ms.id, q.id)*100)/
            getFullPointsBySurveyQuestion(s.id, q.id),0)  as 'Процент выполнения'
            
            from survey s, ms_survey ms, survey_filial sf, scenario sc, filial f, question_answer qa,  question q, question_lang ql, question_check qc
            where 
            sf.survey_id = s.id
            and ms.survey_filial = sf.id
            and f.id = sf.filial_id
            and sc.id = sf.id_scenario
            and ms.status = {$this->finished}
            and s.client_id = {$client_id}
            and qa.ms_survey_id = ms.id
            and q.id = ql.question_id and  ql.lang_id = 'ru' 
            and q.id = qa.question_id
            and qa.id = qc.answer_id"
        ]);
//        print_r($query->createCommand()->getRawSql());
//        die;
        return $query;
    }


    public function getObjectCategoriesProps($client_id, $type, $searchModel = null)
    {
        if (!empty($searchModel->survey_ids)) {
            $searchSurvey = "AND s.id in ({$searchModel->survey_ids})";
        }
        if (!empty($searchModel->filial_ids)) {
            $searchFilial = "AND sf.filial_id in ({$searchModel->filial_ids})";
        }
        switch ($type) {
            case self::TYPE_BY_CITY:
                $query = $this->objectsByCities($client_id, $searchSurvey, $searchFilial, $searchModel->date_from);
                $cityNames = [];
                $cityResults = [];
                foreach ($query->all() as $item) {
                    $cityNames[] = $item['city_name'];
                    $cityResults[] = (float)$item['percent'];
                }
                return ['cityNames' => $cityNames, 'cityResults' => $cityResults];
                break;
            case self::TYPE_BY_FILIAL:
                $query = $this->objectsMain($client_id, $searchSurvey, $searchFilial, $searchModel->date_from);
                $filialNames = [];
                $filialResults = [];
                foreach ($query->all() as $item) {
                    $filialNames[] = $item['name'];
                    $filialResults[] = (float)$item['percent'];
                }
                return ['filialResults' => $filialResults, 'filialNames' => $filialNames];
                break;
        }
    }

    public function scenarios($client_id, $searchModel)
    {
        if (!empty($searchModel->survey_ids)) {
            $searchSurvey = "AND s.id in ({$searchModel->survey_ids})";
        }
        if (!empty($searchModel->filial_ids)) {
            $searchFilial = "AND sf.filial_id in ({$searchModel->filial_ids})";
        }
        $query = $this->queryBuilder->select([
            "s.id, s.survey_from as survey_from, s.survey_to as survey_to,sl.name, sf.id_scenario, scr.name as scenario_name, count(*) as total,sum(case when (mss.status = 0) then 1 else 0 end) status_new, sum(case when (mss.status = 111) then 1 else 0 end) ms_assigned, sum(case when (mss.status = 222) then 1 else 0 end) in_process,
            sum(case when (mss.status = 333) then 1 else 0 end) on_moderation, sum(case when (mss.status = 444) then 1 else 0 end) moderation_start,
            sum(case when (mss.status = 555) then 1 else 0 end) completed
            from `survey` s, `survey_lang` sl, `ms_survey` mss, `survey_filial` sf,
            `scenario` scr
            where  sl.lang_id = 'ru' and sl.survey_id = s.id
            {$searchSurvey} {$searchFilial} {$searchModel->date_from}
            and sf.survey_id = s.id and mss.survey_filial = sf.id
            and s.client_id = {$client_id} and scr.id = sf.id_scenario
            GROUP BY
            sf.id_scenario"
        ]);

        //NEW
//        "q1.survey_name, q1.scenario_name,  AVG(q1.percent), count( *)
//        from (
//        select (select sc.name from scenario sc where sf.id_scenario = sc.id) as scenario_name,
//        (select sl.name from survey_lang sl where sl.survey_id = s.id and sl.lang_id = 'ru') as survey_name,
//        (getRealPointsBySurveyMS(s.id, ms.ms_id)*100)/getMaxPointsBySurvey(s.id) as percent
//        from survey s, ms_survey ms, survey_filial sf, filial f
//        where sf.survey_id = s.id
//        and ms.survey_filial = sf.id
//        and f.id = sf.filial_id
//        and ms.status = {$this->finished}
//        and s.client_id = {$client_id}
//        ) as q1
//        group by q1.survey_name, q1.scenario_name"
        return $query;
    }

    public function getScenariosData($client_id, $searchModel = null)
    {
        $query = $this->scenarios($client_id, $searchModel);

        $chartData = [];
        $flatSeries = [];

        foreach ($query->all() as $item) {
            $chartData[] = [$item['scenario_name'], (float)$item['total']];
            $flatSeries[] = [
                'name' => $item['scenario_name'],
                'data' => [
                    (int)$item['status_new'],
                    (int)$item['ms_assigned'],
                    (int)$item['in_process'],
                    (int)$item['on_moderation'],
                    (int)$item['moderation_start'],
                    (int)$item['completed']
                ]
            ];
        }

        return ['chartData' => $chartData, 'flatSeries' => $flatSeries];
    }

    public function getTreeQueryInterview($msId)
    {
        if (!is_numeric($msId)) {
            throw new NotFoundHttpException(\Yii::t('app', 'Неверные параметры'));
        }
        $query = "select a.id, a.parent_id, al.name, getRealPointsByMSSurveyArticle({$msId}, a.id) as real_point,
        getFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id) as max_point,
        ifnull((getRealPointsByMSSurveyArticle({$msId}, a.id)*100)/
        getFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id),0) as percent,
        
        'no_answer' as text_answer, 'no_answer' as checked_answer,
        'article' as tableName
        from article a, article_lang al
        where a.id = al.article_id and  al.lang_id = 'ru'  and
            a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
        where sf.id = ms.survey_filial
            and s.id = sf.survey_id
            and ms.id = {$msId})
        union ALL
        select q.id, a.id, ql.name, getRealPointsByMSSurveyQuestion({$msId}, q.id),
        getFullPointsBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id),
        ifnull((getRealPointsByMSSurveyQuestion({$msId}, q.id)*100)/
            getFullPointsBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id),0) as percent, qa.text as text_answer,
            aol.text as checked_answer,
        'question' as tableName
        from article a, question_lang ql,
        question q LEFT JOIN question_answer as qa ON (q.id = qa.question_id AND {$msId} = qa.ms_survey_id)
        LEFT JOIN question_check AS qc ON qc.answer_id = qa.id 
        LEFT JOIN answer_option_lang AS aol ON aol.answer_option_id = qc.answer_option_id 
        where q.id = ql.question_id and  ql.lang_id = 'ru'
            and q.article_id = a.id and a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
        where sf.id = ms.survey_filial
            and s.id = sf.survey_id and ms.id = {$msId})";
        return $query;
    }

    public function getTreeQuerySummary($client_id, $survey_id = null)
    {
        if ($survey_id) {
            $andWhere = "and sf.survey_id = {$survey_id})";
        } else {
            $andWhere = "and s.client_id = {$client_id})";
        }

        $query = "select a.id, a.parent_id, al.name, 
        getRealFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id) as real_point,
        getFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id)*getCountBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id) as max_point,
        ifnull((getRealFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id)*100)/
        (getFullPointsBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id)*getCountBySurveyArticle((select s.id from survey s where s.questionary_id = a.questionary), a.id)),0) as percent,
        'article' as tableName
        from article a, article_lang al
        where a.id = al.article_id and  al.lang_id = 'ru'  and 
        a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
        where sf.id = ms.survey_filial 
        and s.id = sf.survey_id
        {$andWhere}
        union ALL
        select  q.id, a.id, ql.name, getRealFullPointsByMSSurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id),
        getFullPointsBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id)*getCountBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id),
        ifnull((getRealFullPointsByMSSurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id)*100)/
        (getFullPointsBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id)*getCountBySurveyQuestion((select s.id from survey s where s.questionary_id = a.questionary), q.id)),0) as percent,
        'question' as tableName
        from article a, question q, question_lang ql
        where q.id = ql.question_id and  ql.lang_id = 'ru' 
        and q.article_id = a.id and a.questionary in (select s.questionary_id from ms_survey ms, survey_filial sf, survey s
        where sf.id = ms.survey_filial 
        and s.id = sf.survey_id {$andWhere}";
        return $query;
    }

    public function getAnswers(int $id)
    {
// Запрос не отображает ответы ТП у которых вопросы без вариантов ответов

//        $query = $this->queryBuilder->select(["
//         mssd.date, f.address, aol.text as sup_text, qa.text as ms_text FROM question_answer AS qa
//INNER JOIN question_check AS qc ON qc.answer_id = qa.id
//INNER JOIN answer_option_lang AS aol ON aol.answer_option_id = qc.answer_option_id
//INNER JOIN ms_survey AS mss ON qa.ms_survey_id = mss.id
//INNER JOIN survey_filial AS sf ON mss.survey_filial = sf.id
//INNER JOIN filial AS f ON sf.filial_id = f.id
//INNER JOIN ms_survey_date AS mssd ON (mssd.ms_survey_id = mss.id and mssd.type = 111)
//WHERE qa.question_id = {$id}"]);

        $query = $this->queryBuilder->select(["
            DISTINCT mssd.date, f.address, aol.text as sup_text, qa.text as ms_text FROM question_answer AS qa 
LEFT JOIN question_check AS qc ON qc.answer_id = qa.id 
LEFT JOIN answer_option_lang AS aol ON aol.answer_option_id = qc.answer_option_id 
LEFT JOIN ms_survey AS mss ON qa.ms_survey_id = mss.id
LEFT JOIN survey_filial AS sf ON mss.survey_filial = sf.id
LEFT JOIN filial AS f ON sf.filial_id = f.id
LEFT JOIN ms_survey_date AS mssd ON (mssd.ms_survey_id = mss.id and mssd.type = 111  and mssd.date is not null)
WHERE qa.question_id = {$id}"]);
        return $query->all();
    }

    public function getAnswerOptionsCount(int $id)
    {
        $query = $this->queryBuilder->select(["
         COUNT(*) as counter, aol.text as sup_text FROM question_answer AS qa  
INNER JOIN question_check AS qc ON qc.answer_id = qa.id 
INNER JOIN answer_option_lang AS aol ON aol.answer_option_id = qc.answer_option_id 
INNER JOIN ms_survey AS mss ON (qa.ms_survey_id = mss.id and mss.status = 555)
WHERE qa.question_id = {$id} GROUP BY sup_text"]);
//        var_dump($query->createCommand()->getRawSql());die;
        return $query->all();
    }

}

