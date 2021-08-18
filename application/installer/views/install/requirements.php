<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this \yii\web\View */
/** @var $requirements array */

$this->title = 'Requirements';
$hasErrors = false;
?>
<p class="text-muted">Requirements</p>
<?php foreach ($requirements as $key => $result): ?>
    <div class="d-flex mb-2 align-items-center justify-content-between alert alert-<?= $result['valid'] ? 'primary' : 'danger' ?>">
        <?= Html::encode($result['title']) ?>
        <span class="badge badge-<?= $result['valid'] ? 'primary' : 'danger' ?> float-right">
            <?= $result['valid'] ? 'pass' : 'fail' ?>
        </span>
    </div>
    <?php if (!$result['valid'] && !$hasErrors) $hasErrors = true ?>
<?php endforeach; ?>

<?= Html::beginForm(['index'], 'post', [
    'class' => 'actions mt-5 d-flex align-items-center justify-content-between',
]) ?>
<?php if ($hasErrors): ?>
    <span class="text-danger"><strong>Warning.</strong> It is not recommended to continue with failed checks</span>
<?php else: ?>
    <span class="text-success">Everything is ok</span>
<?php endif; ?>
<?= Html::submitButton('Continue', ['class' => 'btn btn-primary']) ?>
<?= Html::endForm() ?>

