<?php

/** @var $model \app\models\Guest */

?>
<div class="col-6 col-sm-6 col-md-4 directory-item">
    <?= $this->render('/directory/_item_body', [
        'model' => $model->fromUser,
        'subString' => $model->getLastUpdate(),
    ]) ?>
</div>
