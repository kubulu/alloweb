<?php

use app\helpers\Html;
use app\base\View;
use youdate\widgets\Connect;

/* @var $this \app\base\View */

$this->title = Html::encode($this->themeSetting('homepageTitle'));
$this->context->layout = 'page-wide';
$this->params['body.cssClass'] = 'body-landing-page';
$flash = Yii::$app->session->getFlash('info', false);
if ($flash) {
    $this->registerJs(sprintf('Messenger().post("%s")', Yii::$app->session->getFlash('info')), View::POS_READY);
}
?>
<div class="content content-landing pt-5 pb-5" style="flex: 1">
    <div class="container">
        <div class="card w-100">
            <div class="card-header landing-page-bg"></div>
            <div class="card-body">
                <div class="row row-landing-page">
                    <div class="col-md-8">
                        <div class="landing-page-head d-flex flex-column mt-auto mb-auto">
                            <h1><?= Html::encode($this->themeSetting('homepageTitle')) ?></h1>
                            <div class="subtitle">
                                <?= Html::encode($this->themeSetting('homepageSubTitle')) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="landing-page-auth">
                            <div class="text-center">
                                <?= Html::a(Yii::t('youdate', 'Sign up'), ['/registration/register'], [
                                    'class' => 'btn btn-primary btn-lg',
                                ]) ?>

                                <?= Html::a(Yii::t('youdate', 'Log in'), ['/security/login'], [
                                    'class' => 'btn btn-secondary btn-lg',
                                ]) ?>
                            </div>
                            <div class="text-center">
                                <?= Connect::widget([
                                    'prepend' => Html::tag('div', Yii::t('youdate', 'or'), ['class' => 'landing-or']),
                                    'append' => Html::tag('div', Yii::t('youdate', 'We never post on your behalf.'), [
                                        'class' => 'text-muted',
                                    ]),
                                    'baseAuthUrl' => ['/security/auth'],
                                    'options' => ['class' => 'social-auth social-auth-lp'],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
