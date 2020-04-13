<?php
/**
 * Created by PhpStorm.
 * User: ikorostelev
 * Date: 31.10.2018
 * Time: 10:33
 */

namespace client\models;


use yii\base\Model;

class UploadForm extends Model
{
    public $file;
    public $type;
    public $client_id;


    public function rules()
    {
        return [
            [['file', 'type', 'client_id'], 'required'],
            ['file', 'file', 'extensions' => 'csv'],
            [['type', 'client_id'], 'integer']
        ];
    }


}