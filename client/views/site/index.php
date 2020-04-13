<?php

use yii\bootstrap\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
$commentsClass = $commentsDataProvider->getCount() ? 'table datatable-highlight' : 'table';
?>
<style>
    tfoot {
        display: table-header-group;
    }
</style>
<div class="index">
    <h3 class="index__head mt-0">Активное анкетирование</h3>
    <div class="container-fluid p-0">
        <?php foreach ($activeSurveys as $survey): ?>
            <div class="index__data mb-4" style="background-color: <?php
            switch ($survey['percent_complete']) {
                case (int)$survey['percent_complete'] < 40:
                    echo 'white';
                    break;
                case ((int)$survey['percent_complete'] >= 50 && (int)$survey['percent_complete'] < 70) :
                    echo '#EDB74F';
                    break;
                case (int)$survey['percent_complete'] >= 70 :
                    echo '#C0DA58';
                    break;
                default:
                    echo 'white';
                    break;
            } ?>;">
                <div class="row">
                    <div class="col-sm-6 col-md-3 mb-3 mb-md-0 align-self-end">
                        <small>Название</small>
                        <span style="overflow: hidden; -ms-text-overflow: ellipsis;text-overflow: ellipsis;"><?= $survey['name'] ?></span>
                    </div>

                    <div class="col-sm-6 col-md-3 mb-3 mb-md-0 align-self-end">
                        <small>Период</small>
                        <span><?= "{$survey['survey_from']} - {$survey['survey_to']}" ?></span>
                    </div>

                    <div class="col-sm-6 col-md-2 mb-3 mb-sm-0 align-self-end">
                        <small>Кол-во объектов</small>
                        <span><?= $survey['objects_count'] ?> </span>
                    </div>

                    <div class="col-sm-6 col-md-2 text-md-center align-self-end">
                        <small>Статус выполнения</small>
                        <b><?= round($survey['percent_complete'], 1) . "%" ?></b>
                    </div>

                    <div class="col-md-2 mt-3 mt-md-0 text-sm-center text-md-left align-self-end">
                        <span><?= "{$survey['complete_count']} из {$survey['all_count']} опросов" ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <h3 class="index__head">Комментарии</h3>
    <div class="tbl-block">
        <div class="table-form">
            <div class="modal-comments table-responsive filias__table-wrap">
                <?= GridView::widget([
                    'dataProvider' => $commentsDataProvider,
                    'emptyText' => 'Нет записей.',
                    'summary' => '',
                    'rowOptions' => function ($model, $key, $index, $grid) {
                        return ['data-url' => \yii\helpers\Url::to(['/report/interview', 'id' => $model['id'], 'tab' => 3])];
                    },
                    'tableOptions' => [
                        'class' => $commentsClass
                    ],
                    'showFooter' => true,
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label' => Yii::t('app', 'ID')
                        ],
                        [
                            'attribute' => 'msg_count',
                            'label' => Yii::t('app', 'Кол-во сообщений')
                        ],
                        [
                            'attribute' => 'task_count',
                            'label' => Yii::t('app', 'Кол-во задач')
                        ],
                        [
                            'attribute' => 'last_msg_datetime',
                            'label' => Yii::t('app', 'Дата')
                        ],
                        [
                            'attribute' => 'city_name',
                            'label' => Yii::t('app', 'Город')
                        ],
                        [
                            'attribute' => 'msg_text',
                            'label' => Yii::t('app', 'Комментарий')
                        ],
                        [
                            'attribute' => 'employe_name',
                            'label' => Yii::t('app', 'Сотрудник')
                        ],
                        [
                            'attribute' => 'percent',
                            'format' => 'html',
                            'value' => function ($data) use ($colorMap) {
                                $color = Yii::$app->clientService->getColorByPercent(round($data['percent'], 1), $colorMap);
                                $text = round($data['percent'], 1) . ' %';
                                return "<p style='background: {$color}'><b>{$text}</b></p>";
                            },
                            'label' => Yii::t('app', 'Рейтинг анкеты')
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>

    <h3 class="index__head">Последние опросы</h3>
    <div class="tbl-block">
        <div class="table-form">
            <div class="modal-table table-responsive filias__table-wrap">
                <?= GridView::widget([
                    'dataProvider' => $surveysDataProvider,
                    'emptyText' => 'Нет записей.',
                    'summary' => '',
                    'rowOptions' => function ($model, $key, $index, $grid) {
                        return ['data-url' => \yii\helpers\Url::to(['/report/interview', 'id' => $model['id']])];
                    },
                    'tableOptions' => [
                        'class' => 'table datatable-basic'
                    ],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label' => Yii::t('app', 'ID')
                        ],
                        [
                            'attribute' => 'complete_date',
                            'label' => Yii::t('app', 'Visit date')
                        ],
                        [
                            'attribute' => 'scenario',
                            'label' => Yii::t('app', 'Сценарий')
                        ],
                        [
                            'attribute' => 'survey_name',
                            'label' => Yii::t('app', 'Survey ID')
                        ],
                        [
                            'attribute' => 'city_name',
                            'label' => Yii::t('app', 'Город')
                        ],
                        [
                            'attribute' => 'filial_name',
                            'label' => Yii::t('app', 'Filial ID')
                        ],
                        [
                            'attribute' => 'employee_name',
                            'label' => Yii::t('app', 'Employee ID'),
                            'value' => function ($data) {
                                return $data['employee_name'] ? $data['employee_name'] : '______';
                            }
                        ],

                        [
                            'attribute' => 'task_count',
                            'label' => Yii::t('app', 'Кол-во заданий')
                        ],
                        [
                            'attribute' => 'real_point',
                            'label' => Yii::t('app', 'Real point')
                        ],
                        [
                            'attribute' => 'max_points',
                            'label' => Yii::t('app', 'Max point')
                        ],
                        [
                            'attribute' => 'percent',
                            'format' => 'html',
                            'value' => function ($data) use ($colorMap) {
                                $color = Yii::$app->clientService->getColorByPercent(round($data['percent'], 1), $colorMap);
                                $text = round($data['percent'], 1) . ' %';
                                return "<p style='background: {$color}'><b>{$text}</b></p>";
                            },
                            'label' => Yii::t('app', 'Рейтинг анкеты')
                        ],
                        [
                            'attribute' => 'comment_count',
                            'label' => Yii::t('app', 'Кол-во комментариев')
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $('.modal-table tbody').on('click', 'tr', function () {
        var url = $(this).data('url');
        $('#interview-modal').modal('show')
            .find('#modalContent')
            .load(url);
    });
    $('.modal-comments tbody').on('click', 'tr', function () {
        var url = $(this).data('url');
        $('#interview-modal').modal('show')
            .find('#modalContent')
            .load(url);
    });
</script>
<div id="interview-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div id='modalContent'></div>
        </div>
    </div>
</div>
