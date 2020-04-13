<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 18.07.18
 * Time: 12:16
 */

namespace supervisor\models;


use yii\base\Model;

class AnswersForm extends Model
{
    /**
     * @var array $texts
     */
    public $texts;
    public $q_ids;
    public $ids;
    public $visit_date;

    public function rules()
    {
        return [
            [['texts', 'q_ids'], 'each', 'rule' => ['required']],
            ['q_ids', 'each', 'rule' => ['integer']],
            ['ids', 'each', 'rule' => ['safe']],
            ['visit_date', 'required']
        ];
    }
    public function attributeLabels()
    {
        return [
          'visit_date' => 'Дата визита'
        ];
    }


}