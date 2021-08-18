<?php

use app\helpers\Html;
use app\models\ProfileField;
use app\models\ProfileExtra;

/** @var $this \app\base\View */
/** @var $profile \app\models\Profile */

?>
<?php foreach ($profile->getExtraCategories() as $categoryAlias => $categoryTitle): ?>
    <div class="profile-info-block" data-category="<?= Html::encode($categoryAlias) ?>">
        <div class="text-bold mb-2">
            <?= Html::encode($categoryTitle) ?>
        </div>
        <div class="row">
            <?php foreach ($profile->getExtraFields($profile->user_id, $categoryAlias) as $item): ?>
                <div class="col-6 mb-4">
                    <div class="text-muted mb-2">
                        <?= Html::encode(Yii::t($item['field']->language_category, $item['field']->title)) ?>:
                    </div>
                    <?php if (isset($item['extra']->value)): ?>
                        <div class="text-bolder"><?= $item['field']->formatValue($item['extra']->value) ?></div>
                    <?php else: ?>
                        <div class="text-bolder"><?= Yii::t('youdate', 'Not set') ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>
