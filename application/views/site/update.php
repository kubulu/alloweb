<?php

use app\helpers\Html;

/** @var $this \yii\web\View */

$this->title = Yii::t('app', 'Maintenance');
?>
<script>
    $(document).ready(function () {
        var $btnUpdate = $('.btn-update');
        $btnUpdate.on('click', function(event) {
            var baseUrl = $('meta[name=baseUrl]').attr('content');
            $btnUpdate.addClass('disabled');
            $btnUpdate.attr('disabled', true);
            $btnUpdate.text('<?= Yii::t('app', 'Updating, please wait...') ?>');
            $.ajax({ url: $btnUpdate.attr('href'), data: { runUpdate: 1 }});
            setInterval(function() {
                $.ajax({
                    url: baseUrl + '/site/apply-updates',
                    success: function(data) {
                        if (data.updated) {
                            window.location.href = baseUrl;
                        }
                    }
                })
            }, 1000);
            event.preventDefault();
        });
    });
</script>
<h3><?= Yii::t('app', 'YouDate update') ?></h3>
<div>
    <p class="mb-4">
        <?= Yii::t('app', 'New update is available. Apply update?') ?>
    </p>
    <?= Html::a(Yii::t('app', 'Apply updates'), ['apply-updates', 'runUpdate' => 1], [
        'class' => 'btn btn-primary mb-2 btn-update',
    ]) ?>
</div>
