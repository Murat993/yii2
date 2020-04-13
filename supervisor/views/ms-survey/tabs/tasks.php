<?php
use yii\grid\GridView;
use common\models\Task;
use yii\helpers\Html;
$class = $dataProvider->getCount() ? 'table datatable-basic' : 'table';
$dataProvider->sort = false;
?>

<div class="">
    <?=
    GridView::widget([

        'dataProvider' => $dataProvider,
        'tableOptions' => [
	        'class' => $class
        ],
        'summary' =>'',
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'html',
                'value' => function($data) use ($ms_survey_id, $id) {
                    return Html::a($data->name, 'task-files?task_id=' . $data->id . '&ms_survey_id=' . $ms_survey_id . '&id=' . $id);
                }
            ],
            [
                'attribute' => 'filetype',
                'value' => function ($data) {
                    return Yii::$app->utilityService->getFileTypeLabel($data->filetype);
                }
            ],
            'comment',
            [
                    'label' => Yii::t('app', 'Статус'),
                    'format' => 'raw',
                    'value' => function($model, $x) use ($ms_id, $ms_survey_id){
                        if($model->checkAnswer($ms_id, $ms_survey_id)){
                            return '<i class="glyphicon glyphicon-ok" style="color:green"/>';
                        }else{
                            return '<i class="glyphicon glyphicon-remove" style="color:red"/>';
                        }
                    }
            ],
            [
                    'header' => Yii::t('app', 'Причина невыполнения'),
                    'value' => function($model, $x) use ($ms_id, $ms_survey_id){
                        if($model->checkAnswer($ms_id, $ms_survey_id)){
                           //
                        }else{
                            return $model->getTaskAnswerCommentByMsId($ms_id, $ms_survey_id);
                        }

                    }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Действия',
                'template' => "{upload}",
                'buttons' => [
                    'upload' => function($x, $model) use ($ms_id, $ms_survey_id){
                        $tooltip = $model->isTaskAnswerFileByMsId($ms_id, $ms_survey_id)?Yii::t('app', 'Изменить') : Yii::t('app', 'Выполнить');
                        return Html::a(
                            '<i class="glyphicon glyphicon-cloud-upload" data-tooltip="'
                            . $tooltip
                            . '" data-position="top" title="'.$tooltip.'"></i>', "javascript:void(0)", [
                                'data-pjax' => 1,
                                'class' => 'task-modal-btn',
                                'data-id' => $model->id,
                                'data-ms_survey_id' => $ms_survey_id
                            ]
                        );
                    }
                ]
            ]
        ],
    ]); ?>
</div>
<div id="img-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img id="popup-img" src="//placehold.it/1000x600" class="img-responsive">
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    $(document).ready(function () {
        $(document).on('click', '.img-modal-a', function() {
            $('#popup-img').attr('src', $(this).data('url'));
        });
    })
    $(document).ready(function () {
        $(document).on('click', '.task-modal-btn', function() {
            $('#task-modal').modal('show');            
            $.ajax({
                url : '/ms-survey/render-task-form?task_id='+$(this).data('id')+'&ms_survey_id='+$(this).data('ms_survey_id'),
                type : 'GET',                
                success : function(data) {             
                      $('#task-modal-content').html(data);
                      $("#reject-txa").prop('disabled', true);
                }                
            });
        });
        $(document).on('change', '#reject-cbx', function() {
            if($(this).is(":checked")){
                $("#reject-txa").prop('disabled', false);
                $("#task-file-inp").prop('disabled', true);
            }else {
                $("#reject-txa").prop('disabled', true);
                $("#task-file-inp").prop('disabled', false);
            } 
        });
    })
JS;
$this->registerJs($script);
?>
<?= $this->render('../modals/upload_task') ?>
