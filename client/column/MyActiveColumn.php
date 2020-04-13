<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 27.09.18
 * Time: 10:05
 */

namespace client\column;


use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\helpers\Html;
use Yii;

class MyActiveColumn extends ActionColumn
{
    public function initDefaultButtons()
    {
//        if (!isset($this->buttons['view'])) {
//            $this->buttons['view'] = function ($url, $model, $key) {
//                $options = array_merge([
//                    'title' => Yii::t('yii', 'View'),
//                    'aria-label' => Yii::t('yii', 'View'),
//                    'data-pjax' => '0',
//                ], $this->buttonOptions);
//                return Html::a('<span class="icon-button icn-exit"></span>', $url, $options);
//            };
//        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::tag('span','',
                    [ 'class' =>'icon-button edit modalUpdateButton',
                        'data-url' => $url,
                        'title' => $options['title'],
                        'aria-label' => $options['aria-label'],
                        'data-pjax' => $options['data-pjax'],
                    ]);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                return Html::a('<span class="icon-button delete"></span>', $url, $options);
            };
        }
    }
}