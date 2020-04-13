<?php

use execut\widget\TreeView;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

?>
<div class="user-update">

    <div class="panel panel-body">

        <?php $form = ActiveForm::begin([]); ?>

        <div class="form-group">
            <?php
            if(!$tree){
                $tree = [];
            }
            $onCheck = new JsExpression(<<<JS
                function (undefined, item) {
                    
                }
JS
            );
            $groupsContent = TreeView::widget([
                'data' => $tree,
                'size' => TreeView::SIZE_NORMAL,
                'header' => 'Объекты',
                'searchOptions' => [
                    'inputOptions' => [
                        'placeholder' => 'Поиск объектов...'
                    ],
                ],
                'clientOptions' => [
                'onNodeChecked' => $onCheck,
//            'selectedBackColor' => 'rgb(40, 153, 57)',
                    'borderColor' => '#fff',
                    'showCheckbox' => true,
                    'checkedIcon' => "glyphicon glyphicon-check"
//            'checkboxes' => true
                ],
            ]);
            echo $groupsContent;

            ?>
            <input type="hidden" name="filial_ids" id="filial-ids-input">
        </div>
        <div class="form-group">
            <?= Html::button(Yii::t('app', 'Сохранить'), [
                'class' => 'btn btn-success',
                'type' => 'button',
                'id' => 'perm-pre-submit'
            ]) ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Сохранить'), [
                'class' => 'btn btn-success',
                'style' => 'display:none',
                'id' => 'perm-submit'
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>



    </div>

</div>
<?php
$script = <<< JS
    $(document).ready(function () {
        var treeSelector = $('#w1');        
        $(document).on('click', '#perm-pre-submit', function () {
            var treeSelector = $('#w1');
                    var arr = treeSelector.treeview('getChecked');
                    var val = [];
                    for (i = 0; i < arr.length; i++) {
                         val.push(arr[i].id_filial); 
                    }
                    $('#filial-ids-input').val(val); 
            $('#perm-submit').click();
        });  
        treeSelector.treeview('collapseAll', { silent: true });
        treeSelector.treeview('revealNode', [ treeSelector.treeview('getChecked'), { silent: true } ]);        

    })
JS;
$this->registerJs($script);
?>

