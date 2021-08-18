<?php

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

/** @var $this \app\base\View */

?>
<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-auto m-auto ml-md-auto">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <ul class="list-inline list-inline-dots mb-2 text-center">
                            <li class="list-inline-item">
                                <?= Html::a(Yii::t('youdate', 'About'), ['/site/page', 'view' => 'about']) ?>
                            </li>
                            <li class="list-inline-item">
                                <?= Html::a(Yii::t('youdate', 'Terms'), ['/site/page', 'view' => 'terms-and-conditions']) ?>
                            </li>
                            <li class="list-inline-item">
                                <?= Html::a(Yii::t('youdate', 'Privacy'), ['/site/page', 'view' => 'privacy-policy']) ?>
                            </li>
                            <li class="list-inline-item">
                                <?= Html::a(Yii::t('youdate', 'Cookie Policy'), ['/site/page', 'view' => 'cookie-policy']) ?>
                            </li>
                        </ul>
                        <ul class="list-inline text-center">
                            <?php if ($this->themeSetting('linksFacebook')): ?>
                                <li class="list-inline-item">
                                    <?= Html::a(FA::icon('facebook-square', ['class' => 'pr-1']) . 'Facebook', $this->themeSetting('linksFacebook'), [
                                        'target' => '_blank',
                                        'rel' => 'tooltip',
                                    ]) ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($this->themeSetting('linksTwitter')): ?>
                                <li class="list-inline-item">
                                    <?= Html::a(FA::icon('twitter', ['class' => 'pr-1']) . 'Twitter', $this->themeSetting('linksTwitter'), [
                                        'target' => '_blank',
                                        'rel' => 'tooltip',
                                    ]) ?>
                                </li>
                            <?php endif; ?>
                            <li class="list-inline-item">
                                <?= $this->frontendSetting('siteName', 'YouDate') ?> &copy; <?= date('Y') ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
