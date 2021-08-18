<?php

/** @var $this \app\base\View */

?>
<?php if (isset($this->params['user.confirmed']) && !$this->params['user.confirmed']): ?>
    <div class="alert alert-warning mb-0">
        <div class="container">
            <strong><?= Yii::t('youdate', 'Warning') ?>.</strong>
            <?= Yii::t('youdate', 'Your e-mail {email} is not confirmed yet. Please check your inbox and confirm.', [
                'email' => '<strong>' . $this->params['user.email'] . '</strong>',
            ]) ?>
        </div>
    </div>
<?php endif; ?>
