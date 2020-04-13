<?php
$this->context->layout = 'error';
?>
<!-- Error title -->
<div class="text-center content-group">
    <h1 class="error-title"><?= $exception->statusCode ?></h1>
    <h5><?php switch ($exception->statusCode) {
            case '404':
                echo Yii::t('app', 'Page not found');
                break;
            case '403':
                echo Yii::t('app', "You're not allowed");
                break;
            default:
                \yii\helpers\Html::encode($meesage);
                break;
        }
        ?>
    </h5>
</div>
<!-- /error title -->


<!-- Error content -->

<!-- /error wrapper -->
