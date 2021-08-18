<?php

/* @var $this \yii\web\View */use app\helpers\Html;
/* @var $counters array */
/* @var $info array */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['index']];
?>

<?php if (Yii::$app->session->hasFlash('updateSuccess')): ?>
<div class="alert alert-success">
    <?= Html::encode(Yii::$app->session->getFlash('updateSuccess')) ?>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'Users') ?></span>
                <span class="info-box-number"><?= $counters['users'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-purple"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'Online') ?></span>
                <span class="info-box-number"><?= $counters['usersOnline'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-photo"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'Photos') ?></span>
                <span class="info-box-number"><?= $counters['photos'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-photo"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"><?= Yii::t('app', 'Photos to verify') ?></span>
                <span class="info-box-number"><?= $counters['photosUnverified'] ?></span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'App info') ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><?= Yii::t('app', 'Version') ?></td>
                        <td><?= $info['version'] ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Environment') ?></td>
                        <td><span class="badge bg-yellow"><?= $info['environment'] ?></span></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Debug') ?></td>
                        <td><span class="badge bg-<?= $info['debug'] ? 'red' : 'green' ?>">
                                <?= $info['debug'] ? 'yes' : 'no' ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'System Info') ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><?= Yii::t('app', 'Memory limit') ?></td>
                        <td><?= $info['memoryLimit'] ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Time limit') ?></td>
                        <td><?= $info['timeLimit'] ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Upload max filesize') ?></td>
                        <td><?= $info['uploadMaxFilesize'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app', 'Cron') ?></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <td><?= Yii::t('app', 'Last hourly cron') ?></td>
                        <td>
                            <?php if ($info['cronHourly']): ?>
                                <span class="badge bg-green">
                                <?= date("Y-m-d H:i:s",$info['cronHourly']) ?>
                            </span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?= Yii::t('app', 'never') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Last daily cron') ?></td>
                        <td>
                            <?php if ($info['cronDaily']): ?>
                                <span class="badge bg-green">
                                <?= date("Y-m-d H:i:s",$info['cronDaily']) ?>
                            </span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?= Yii::t('app', 'never') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#cron-setup">
                                <?= Yii::t('app', 'Cron setup') ?>
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cron-setup"
     tabindex="-1" role="dialog" aria-labelledby="cron-setup-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cron-setup-title">
                    <?= Yii::t('app', 'Cron setup') ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= Yii::t('app', 'Daily cron command') ?>: <code>./yii cron/daily</code></p>
                <p><?= Yii::t('app', 'Hourly cron command') ?>: <code>>./yii cron/hourly</code></p>
                <br>
                <p><strong><?= Yii::t('app', 'Example') ?>:</strong></p>
                <code style="display: block">
                    30 * * * * /path/to/youdate/application/yii cron/hourly >/dev/null 2>&1 <br>
                    0 18 * * * /path/to/youdate/application/yii cron/daily >/dev/null 2>&1 <br>
                    * * * * * /path/to/youdate/application/yii queue/run >/dev/null 2>&1
                </code>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?= Yii::t('app', 'OK') ?>
                </button>
            </div>
        </div>
    </div>
</div>

