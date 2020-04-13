<?php
$color = Yii::$app->clientService->getColorByPercent(round($category['percent'], 1), $colorMap);

?>
<div class="interview__list-items">
    <div class="interview__item">
        <div class="interview__info">
            <span class="interview__title">

                <?php if ($category['tableName'] == 'article') {
                    echo "<b><u>{$category['name']}</u></b>";
                } else {
                    echo "{$category['name']}";
                } ?>
                <p>
                      <?php if ($category['checked_answer'] && $category['checked_answer'] != 'no_answer'): ?>
                          <b><?= "Ответ: {$category['checked_answer']}"; ?></b>
                      <?php endif; ?>
                    <?php if ($category['text_answer'] && $category['text_answer'] != 'no_answer'): ?>
                        <mark><em><?= "Комментарий ТП: {$category['text_answer']}"; ?></em></mark>
                    <?php endif; ?>
                    </p>
                <h6
                    <?php if ($category['tableName'] == 'question'): ?>
                        class="mylink" data-url="/report/question?name=<?= urlencode($category['name']) ?>&real_point=<?= $category['real_point'] ?>&max_point=<?= $category['max_point'] ?>&percent=<?= $category['percent'] ?>&question_id=<?= $category['id'] ?>"
                    <?php endif ?>
                        style="background: <?= $color ?>">
                    <?= "Баллов ({$category['real_point']} из {$category['max_point']}) - " . round($category['percent'], 1) . "%" ?>
                 </h6>
            </span>
            <?php if (!empty($category['childs'])): ?>
                <span class="interview__icon"><i class="icn icn-pls"></i></span>
            <?php endif; ?>

        </div>
    </div>
    <?php if (!empty($category['childs'])): ?>
        <div class="interview__childs">
            <?= $this->getMenuHtml($category['childs']) ?>
        </div>
    <?php endif; ?>
</div>