<?php
/**
 * Created by PhpStorm.
 * User: narims
 * Date: 18.07.18
 * Time: 10:27
 */
$questions = Yii::$app->questService->getQuestionsListByMsSupervisorArticle($data->id, $ms_survey_id, $supervisor_id);
?>
<?= "{$data->name} ({$data->code})" ?>
<?php if ($questions): ?>
    <a style="float: right" href="javascript:void(0)" data-toggle="collapse" data-target="#demo<?= $data->id ?>">
        <u><i><?= Yii::t('app', 'Вопросы') ?></i></u></a>
    <br>
    <br>


    <div id="demo<?= $data->id ?>" class="collapse">
        <ul class="ms-info__list">
            <li class="ms-info__item supervisor__list-header">
                <div class="row">
                    <div class="col-md-4">
                        <div class="ms-info__name"><?= Yii::t('app', 'Вопрос'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="ms-info__name"><?= Yii::t('app', 'Ответ') ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="ms-info__name"><?= Yii::t('app', 'Статус') ?></div>
                    </div>
                </div>
            </li>


            <?php foreach ($questions as $qn): ?>
                <li class="ms-info__item supervisor__item">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="ms-info__value"><?= $qn->name ?></div>
                        </div>
                        <div class="col-md-4">
                            <code style="color: black; background-color: lightcyan"><?= $qn->questionAnswer->text ?: '______' ?></code>
                        </div>
                        <div class="col-md-4">
                            <div class="ms-info__value">
                                <?php
                                $qnAnswer = $qn->questionAnswer;
                                if (!$qnAnswer) {
                                    $qnAnswer = $qn->getAnswerByMs($ms_id, $ms_survey_id);
                                }
                                $qnCheck = $qnAnswer->questionCheck;
                                if ($qnCheck)
                                    $isChecked = true;
                                if (!$qnCheck || $qnCheck->comment) {
                                    $isChecked = false;
                                }
                                $colorClass = $isChecked ? "success" : "warning";
                                $comment = $qnCheck->comment ? "comment" : "no-comment";
                                $icon = $isChecked ? "check" : "unchecked";
                                $textLabel = $isChecked ? Yii::t('app', "Проверено")
                                    : Yii::t('app', "Не проверено");
                                ?>
                                <a class="text-<?= $colorClass ?> moderate-modal-a <?= $comment ?>" data-q-id="<?= $qn->id ?>"
                                   data-a-id="<?= $qnAnswer->id ?>" data-completed="<?= $completed ?>"
                                   style="" href="javascript:void(0)">
                                    <i class="glyphicon glyphicon-<?= $icon ?>"></i><?= $textLabel ?></a>
                                <?php if ($qnCheck->comment): ?>
                                    <a class="ms-info__name" style="margin-left: 20px" data-container="body"
                                       data-toggle="popover" data-placement="top"
                                       data-content="<?= $qnCheck->comment ?>">
                                        Комментарий
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>





