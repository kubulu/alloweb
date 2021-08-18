<?php

use youdate\widgets\EmptyState;

?>
<div class="h-100 ng-hide"
     ng-show="initialStateLoaded === true && !hasEncounters()">
    <div class="no-contacts d-flex h-100 p-5 justify-content-center">
        <?= EmptyState::widget([
            'icon' => 'fe fe-users',
            'title' => Yii::t('youdate', 'Users not found'),
            'subTitle' => Yii::t('youdate', 'You can try to narrow your search filters'),
        ]) ?>
    </div>
</div>
