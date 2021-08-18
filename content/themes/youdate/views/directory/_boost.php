<?php

use app\helpers\Html;

?>
<div class="d-sm-none d-lg-block col-md-12 mb-5">
    <div class="directory-banner p-5 d-flex justify-content-between align-items-center">
        <h4 class="mb-0 d-flex flex-row align-items-center">
            <div class="w-6 h-6 rounded mr-3 justify-content-center align-items-center d-none d-sm-flex boost-up">
                <i class="fa fa-arrow-up"></i>
            </div>
            <?= Yii::t('youdate', 'Rise up your profile in search and get new connections faster') ?>
        </h4>
        <?= Html::a('<i class="fa fa-arrow-up"></i><span class="ml-2 d-none d-sm-inline">' . Yii::t('youdate', 'Rise up') . '</span>',
            ['/balance/services'],
            ['class' => 'btn btn-primary btn-lg float-right d-flex justify-content-between align-items-center']) ?>
    </div>
</div>
