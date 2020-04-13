<?php
use yii\helpers\Url;
/**
 * Just for ajax rendering
 */

use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(['options' => [
        'id' => 'upl-task-form',
        'enctype' => 'multipart/form-data',
        'action' => Url::to(['save-task-answer', 'task_id' => $model->task_id, 'survey_id' => $survey_id, 'ms_survey_id' => $ms_survey_id]),
        'data-url' => Url::to(['save-task-answer', 'task_id' => $model->task_id, 'survey_id' => $survey_id, 'ms_survey_id' => $ms_survey_id]),
        'method' => 'post'
]]) ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 id="modal-title" class="modal-title"><?= $title ?></h4>
</div>
<div class="modal-body">
    <?= $form->field($model, 'file[]')->fileInput(['multiple' => true, 'id'=>'task-file-inp']) ?>
    <p id="file-rules-text"><?= $description ?></p>
    <?= $form->field($model, 'reject')->checkbox([
        'id'=>'reject-cbx'
    ]) ?>
    <?= $form->field($model, 'comment')->textarea([
        'id' => 'reject-txa',
        'disabled' => true,
        'placeholder' => Yii::t('app',"Укажите причину по которой задание не может быть
выполнено")]) ?>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-success"><?= Yii::t('app', 'Сохранить') ?></button>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Закрыть') ?></button>
</div>
<?php ActiveForm::end() ?>
<script>
    $("#upl-task-form").attr('action', $("#upl-task-form").data("url"))
</script>
