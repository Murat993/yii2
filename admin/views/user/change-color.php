<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="panel panel-body">
<h4>Добавить цвет</h4>

<div class="client-color-form">
    <?php $form = ActiveForm::begin();?>
      <?php if (!empty($globalColor)):
        foreach ($globalColor as $key => $value):?>
            <div class="color-block" style="display:flex">
                <?= $form->field($value, "[$key]color")->textInput(
                    ['type'=>'text',
                     'value'=> $value['color'],
                     'class'=>'color-input'])->label(false);?>

                <?= $form->field($value, "[$key]procent")->textInput([
                    'type'=>'number',
                    'class'=>'procent-input',
                    'min'=>0,
                    'max'=>100])->label(false); ?>

                <?= Html::button(Yii::t('app', '-'), ['class' => 'btn btn-info delete-color', 'data-id'=> $globalColor[$key]->id]) ?>

            </div>
        <?php endforeach; ?>
      <?php endif; ?>
</div>

<div class="form-group">
    <?= Html::button(Yii::t('app', '+'), ['class' => 'btn btn-success add-color']) ?>

    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success save-color']) ?>
</div>

<?php ActiveForm::end();?>

</div>

<?php
$script = <<< JS
    $(document).ready(function() {
        // динамическое добавление инпута цвета и процента
      $('.add-color').on('click', function() {
        var div = $('<div>').attr({ class: 'color-block',style:'display: flex'});
        var inputColor = $('<input/>').attr({ 
        type: 'text', 
        class: 'change-color form-group color-input', 
        value: "#5F5", 
        name: 'color'});
        var inputProcent = $('<input/>').attr({ 
        type: 'number', 
        class: 'change-procent form-group procent-input', 
        name: 'procent',
        value: 10,
        min:0,
        max:100});
        $('.change-color', '.change-procent').wrapAll("<div class='from-group'></div>");
        $('form').append(div);
        div.append(inputColor);
        div.append(inputProcent);
        inputColor.each(function(i,elem) {
           var hueb = new Huebee( elem, {});
        })
      });
      // сохранение динамических инпутов    
      $('.save-color').on('click', function() {
          var validatePercent = true;
          var color = $('.change-color');
          var procent = $('.change-procent');
          if (color.length !== 0) {
             var colorArr = [];
             var procentArr = [];
                 color.each(function() {
                 colorArr.push($(this).val());
                 });
                 procent.each(function() {
                 procentArr.push($(this).val());
                 $(this).val() > 100 ? validatePercent = false : true;
                 });
                 if (validatePercent) {
                  $.ajax({
                    type: 'GET',
                    url: "/user/color",
                    data: {
                        color: colorArr,
                        procent: procentArr
                    }, 
                    success: function (data) {
                    }
                 })
             }
          }
      });
      // удаление цвета
      $(document).on('click','.delete-color', function() {
          var colorId = $(this).data('id');
          var that = $(this);
          $.ajax({
            type: 'GET',
            url: "/user/delete-color",
            data: {
                colorId: colorId,
            },
            success: function (data) {
                that.parents('.color-block').hide(300);
            }
         })
      });
      // валидация инпута процента
     $(document).on('keypress','.procent-input',function (e){
      var charCode = (e.which) ? e.which : e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
          }
        });
     $('.color-input').each( function( i, elem ) {
          var hueb = new Huebee( this, {});
        });
    })
JS;
$this->registerJs($script);
?>
<style>
    .color-input {
        width: 80px;
        height: 45px;
    }
    .procent-input {
        width: 70px;
        height: 45px;
    }
    .delete-color {
        height: 40px;
        margin-left: 15px
    }
</style>