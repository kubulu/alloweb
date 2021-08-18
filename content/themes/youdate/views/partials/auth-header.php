<?php

use app\helpers\Html;
use app\helpers\Url;

/** @var $this \app\base\View */

?>
<div class="text-center mb-3">
    <a href="<?= Url::to(['/']) ?>">
        <?= Html::img($this->themeSetting('logoUrl', '@themeUrl/static/images/logo@2x.png'), [
            'style' => 'max-height: 40px',
            'alt' => $this->frontendSetting('siteName'),
        ]) ?>
    </a>
</div>
