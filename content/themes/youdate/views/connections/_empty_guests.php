<?php

use app\helpers\Html;

?>
<div class="container">
    <div class="card">
        <div class="card-body">
            <?= \youdate\widgets\EmptyState::widget([
                'icon' => 'fe fe-users',
                'subTitle' => Yii::t('youdate', 'No one has visited your profile yet'),
                'action' => Html::a(Yii::t('youdate', 'Browse people'), ['/directory/index'], [
                    'class' => 'btn btn-primary',
                ]),
            ]) ?>
        </div>
    </div>
</div>
