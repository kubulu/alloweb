<?php

use installer\forms\ConfigForm;
use yii\helpers\Html;

/** @var $this \yii\web\View */
/** @var $model \installer\forms\ConfigForm */

$this->title = 'Configuration';
$this->registerJs('
$(document).ready(function() {
    $(".mailer-select").on("change", function() {
        if ($(this).val() == "smtp") {
            $("#mailer-options").removeClass("hide");
        } else {
            $("#mailer-options").addClass("hide");
        }
    });
});
', \yii\web\View::POS_END)
?>

<?= Html::beginForm(['index'], 'post') ?>
<?= Html::errorSummary($model, ['class' => 'alert alert-danger']) ?>

<p class="text-muted">Administration:</p>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label>Admin login:</label>
            <?= Html::activeTextInput($model, 'adminUsername', ['class' => 'form-control']) ?>
            <small class="form-text text-muted">
                Default is <code>admin</code>
            </small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label>Admin e-mail:</label>
            <?= Html::activeTextInput($model, 'adminEmail', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label>Admin password:</label>
            <?= Html::activePasswordInput($model, 'adminPassword', ['class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Application URL:</label>
            <?= Html::activeTextInput($model, 'appUrl', ['class' => 'form-control']) ?>
            <small class="form-text text-muted">
                For example: <code>https://youdate-demo.hauntd.me/</code>
            </small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Admin area URL part:</label>
            <?= Html::activeTextInput($model, 'adminPrefix', ['class' => 'form-control']) ?>
            <small class="form-text text-muted">
                Default is <code>admin</code>
            </small>
        </div>
    </div>
</div>


<p class="text-muted mt-5">Mailer</p>
<div class="form-group">
    <label>Mailer transport:</label>
    <?= Html::activeDropDownList($model, 'appMailerTransport', [
        ConfigForm::MAILER_SMTP => 'SMTP',
        ConfigForm::MAILER_SENDMAIL => 'Sendmail',
        ConfigForm::MAILER_PHP_MAIL => 'PHP Mail',
        ConfigForm::MAILER_FILE => 'File Transport (for development)',
    ], ['class' => 'form-control mailer-select', 'prompt' => '-- Select mailer transport --']) ?>
</div>
<div class="row <?= $model->appMailerTransport !== ConfigForm::MAILER_SMTP ? 'hide' : '' ?>" id="mailer-options">
    <div class="col-sm-4">
        <div class="form-group">
            <label>SMTP Host</label>
            <?= Html::activeTextInput($model, 'appMailerHost', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label>SMTP Port</label>
            <?= Html::activeTextInput($model, 'appMailerPort', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label>SMTP Encryption</label>
            <?= Html::activeDropDownList($model, 'appMailerEncryption', [
                'ssl' => 'SSL',
                'tls' => 'TLS',
            ], ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>SMTP Username</label>
            <?= Html::activeTextInput($model, 'appMailerUsername', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>SMTP Password</label>
            <?= Html::activeTextInput($model, 'appMailerPassword', ['class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Mail From</label>
    <?= Html::activeTextInput($model, 'appMailFrom', ['class' => 'form-control']) ?>
    <small class="form-text text-muted">
        Example: <code>noreply@youdate.hauntd.me</code>
    </small>
</div>

<p class="text-muted mt-5">Social auth</p>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            <label>Facebook Client ID</label>
            <?= Html::activeTextInput($model, 'facebookAppId', ['class' => 'form-control']) ?>
            <small class="form-text text-muted">
                More info at <a href="https://developers.facebook.com/">developers.facebook.com</a>
            </small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Facebook Client ID</label>
            <?= Html::activeTextInput($model, 'facebookAppSecret', ['class' => 'form-control']) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Twitter Consumer Key</label>
            <?= Html::activeTextInput($model, 'twitterConsumerKey', ['class' => 'form-control']) ?>
            <small class="form-text text-muted">
                More info at <a href="https://developer.twitter.com/">developer.twitter.com</a>
            </small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label>Twitter Consumer Secret</label>
            <?= Html::activeTextInput($model, 'twitterConsumerSecret', ['class' => 'form-control']) ?>
        </div>
    </div>
</div>

<div class="actions mt-5 d-flex align-items-center justify-content-end">
    <?= Html::submitButton('Finish', ['class' => 'btn btn-primary']) ?>
</div>

<?= Html::endForm(); ?>
